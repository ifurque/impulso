<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\AppointmentConfirmation;
use Carbon\Carbon;

/**
 * Panel de gestión del emprendimiento: turnos, pedidos, consultas, base de datos de productos y miembros.
 * Incluye también los endpoints públicos para sacar y administrar turnos con token.
 */
class ManagementController extends Controller
{
    private const DATE_NOT_PAST_RULE = 'after_or_equal:today';
    private const SLOT_UNAVAILABLE_MESSAGE = 'El horario seleccionado ya no está disponible.';
    private const DATABASE_OPTIONAL_COMPONENTS = ['producto', 'marca', 'modelo', 'unidad', 'cantidad', 'precio', 'categoria'];

    /** Resumen de gestión: últimos turnos, pedidos (envío y retiro) y consultas. */
    public function index(Business $business)
    {
        $this->ownerOnly($business);
        return view('management.index', [
            'business' => $business,
            'appointments' => $business->appointments()->with(['client', 'product'])->latest('appointment_date')->limit(12)->get(),
            'deliveryOrders' => $business->orders()->with('product')->where('delivery_method', 'delivery')->latest()->limit(12)->get(),
            'pickupOrders' => $business->orders()->with('product')->where('delivery_method', 'pickup')->latest()->limit(12)->get(),
            'inquiries' => $business->inquiries()->with('client')->latest()->limit(20)->get(),
        ]);
    }

    /** Muestra la tabla de productos con las columnas (componentes) que el dueño eligió ver. */
    public function database(Business $business)
    {
        $this->ownerOnly($business);

        $selectedComponents = $business->database_components;
        if (!$selectedComponents || !is_array($selectedComponents)) {
            $selectedComponents = self::DATABASE_OPTIONAL_COMPONENTS;
        }

        return view('management.database', [
            'business' => $business->load(['products' => fn ($query) => $query->orderByDesc('id')]),
            'availableComponents' => self::DATABASE_OPTIONAL_COMPONENTS,
            'selectedComponents' => $selectedComponents,
        ]);
    }

    /** Guarda qué columnas opcionales se muestran en la base de datos de productos. */
    public function updateDatabaseComponents(Request $request, Business $business)
    {
        $this->ownerOnly($business);

        $data = $request->validate([
            'components' => ['nullable', 'array'],
            'components.*' => ['string', 'in:'.implode(',', self::DATABASE_OPTIONAL_COMPONENTS)],
        ]);

        $selected = collect($data['components'] ?? [])->unique()->values()->all();

        $business->update(['database_components' => $selected]);

        return back()->with('success', 'Componentes de la base de datos actualizados.');
    }

    /** Lista los miembros asociados al emprendimiento. */
    public function members(Business $business)
    {
        $this->ownerOnly($business);

        return view('management.members', ['business' => $business->load('members')]);
    }

    /**
     * Solicita un turno. Si hay sesión queda pendiente; si es invitado queda a la espera de
     * confirmación por mail. Se revalida el horario dentro de una transacción con bloqueo para evitar dobles reservas.
     */
    public function appointment(Request $request, Business $business)
    {
        $rules = ['product_id' => ['nullable', 'exists:products,id'], 'appointment_date' => ['required', 'date', self::DATE_NOT_PAST_RULE], 'start_time' => ['required', 'date_format:H:i'], 'notes' => ['nullable', 'string', 'max:500']];
        abort_unless($business->appointments_enabled, 422, 'Los turnos no están habilitados para este emprendimiento.');
        $data = $request->validate($rules);
        $date = Carbon::parse($data['appointment_date']);
        $slots = $this->availableSlotTimes($business, $date);
        abort_unless(in_array($data['start_time'], $slots, true), 422, self::SLOT_UNAVAILABLE_MESSAGE);
        abort_unless(empty($data['product_id']) || $business->products()->whereKey($data['product_id'])->exists(), 422, 'La propuesta seleccionada no pertenece a este emprendimiento.');
        $data['end_time'] = Carbon::createFromFormat('H:i', $data['start_time'])
            ->addMinutes($business->appointment_slot_duration ?? 30)->format('H:i');

        if ($request->user()) {
            $data += ['client_id' => $request->user()->id, 'status' => 'pending'];
            $recipient = $request->user()->email;
            $name = $request->user()->name;
        } else {
            $guestData = $request->validate(['guest_name' => ['required', 'string', 'max:100'], 'guest_email' => ['required', 'email', 'max:180']]);
            $data += $guestData;
            $data += ['guest_name' => $data['guest_name'], 'guest_email' => $data['guest_email'], 'status' => 'awaiting_confirmation'];
            $recipient = $data['guest_email'];
            $name = $data['guest_name'];
        }
        $appointment = DB::transaction(function () use ($business, $data) {
            $lockedBusiness = Business::whereKey($business->id)->lockForUpdate()->first();
            $date = Carbon::parse($data['appointment_date']);
            abort_unless(in_array($data['start_time'], $this->availableSlotTimes($lockedBusiness, $date), true), 422, self::SLOT_UNAVAILABLE_MESSAGE);

            return $lockedBusiness->appointments()->create($data + [
                'confirmation_token' => Str::random(64),
                'management_token' => Str::random(64),
            ]);
        });
        $appointment->setAttribute('guest_name', $name);
        Mail::to($recipient)->send(new AppointmentConfirmation($appointment));
        return back()->with('success', 'Revisa tu correo para confirmar el turno.');
    }

    /** Devuelve en JSON los horarios libres de un emprendimiento para una fecha. */
    public function availableSlots(Business $business, Request $request)
    {
        abort_unless($business->is_public && $business->appointments_enabled, 404);
        $data = $request->validate(['date' => ['required', 'date', self::DATE_NOT_PAST_RULE]]);

        return response()->json(['slots' => $this->availableSlotTimes($business, Carbon::parse($data['date']))]);
    }

    /** Confirma el turno de un invitado a partir del enlace que recibió por mail. */
    public function confirmAppointment(string $token)
    {
        $appointment = \App\Models\Appointment::where('confirmation_token', $token)->firstOrFail();
        $appointment->update(['status' => 'pending', 'confirmed_at' => now(), 'confirmation_token' => null]);
        return redirect()->route('business.show', $appointment->business)->with('success', 'Turno confirmado. El emprendimiento recibió tu solicitud.');
    }

    /** Pantalla para que el cliente vea, cancele o reprograme su turno con el token de gestión. */
    public function manageAppointment(string $token)
    {
        $appointment = \App\Models\Appointment::where('management_token', $token)->firstOrFail();
        return view('appointments.manage', compact('appointment'));
    }

    /** Cancela el turno desde el enlace de gestión del cliente. */
    public function cancelAppointment(string $token)
    {
        $appointment = \App\Models\Appointment::where('management_token', $token)->firstOrFail();
        $appointment->update(['status' => 'cancelled']);
        return redirect()->route('appointments.manage', $token)->with('success', 'El turno fue cancelado.');
    }

    /** Cambia fecha y hora del turno, validando que el nuevo horario esté libre (ignorando el propio turno). */
    public function rescheduleAppointment(Request $request, string $token)
    {
        $appointment = \App\Models\Appointment::where('management_token', $token)->firstOrFail();
        $data = $request->validate([
            'appointment_date' => ['required', 'date', self::DATE_NOT_PAST_RULE],
            'start_time' => ['required', 'date_format:H:i'],
        ]);
        $date = Carbon::parse($data['appointment_date']);
        abort_unless(in_array($data['start_time'], $this->availableSlotTimes($appointment->business, $date, $appointment->id), true), 422, self::SLOT_UNAVAILABLE_MESSAGE);
        $appointment->update([
            'appointment_date' => $data['appointment_date'],
            'start_time' => $data['start_time'],
            'end_time' => Carbon::createFromFormat('H:i', $data['start_time'])->addMinutes($appointment->business->appointment_slot_duration ?? 30)->format('H:i'),
        ]);
        return redirect()->route('appointments.manage', $token)->with('success', 'El turno fue reprogramado.');
    }

    /** El dueño actualiza el estado de un turno. */
    public function appointmentStatus(Request $request, Business $business, $appointment)
    {
        $this->ownerOnly($business);
        $data = $request->validate(['status' => ['required', 'in:pending,confirmed,cancelled,completed']]);
        $business->appointments()->findOrFail($appointment)->update($data);
        return back()->with('success', 'Estado del turno actualizado.');
    }

    /** Un usuario con sesión envía una consulta al emprendimiento. */
    public function inquiry(Request $request, Business $business)
    {
        abort_unless($request->user(), 403);
        $data = $request->validate(['subject' => ['required', 'string', 'max:120'], 'message' => ['required', 'string', 'max:1000']]);
        $business->inquiries()->create($data + ['client_id' => $request->user()->id]);
        return back()->with('success', 'Consulta enviada.');
    }

    /** El dueño responde o cambia el estado de una consulta; para marcarla respondida exige texto de respuesta. */
    public function inquiryStatus(Request $request, Business $business, $inquiry)
    {
        $this->ownerOnly($business);
        $data = $request->validate([
            'status' => ['required', 'in:pending,answered,closed'],
            'response' => ['nullable', 'string', 'max:1000'],
        ]);

        $inquiryModel = $business->inquiries()->findOrFail($inquiry);
        $status = $data['status'];
        $response = trim((string) ($data['response'] ?? ''));

        if ($status === 'answered' && $response === '') {
            return back()->withErrors(['response' => 'Escribe una respuesta antes de marcar la consulta como respondida.']);
        }

        $inquiryModel->update([
            'status' => $status,
            'response' => $response !== '' ? $response : null,
            'answered_at' => $status === 'answered' ? now() : null,
        ]);

        return back()->with('success', 'Consulta actualizada.');
    }

    /** El dueño actualiza el estado de un pedido. */
    public function orderStatus(Request $request, Business $business, $order)
    {
        $this->ownerOnly($business);
        $data = $request->validate(['status' => ['required', 'in:pending,confirmed,delivered,cancelled']]);
        $business->orders()->findOrFail($order)->update($data);
        return back()->with('success', 'Estado del pedido actualizado.');
    }

    /** Asocia un usuario existente (por email) al emprendimiento con un rol. */
    public function member(Request $request, Business $business)
    {
        $this->ownerOnly($business);
        $data = $request->validate(['email' => ['required', 'email', 'exists:users,email'], 'role' => ['required', 'string', 'max:60']]);
        $user = User::where('email', $data['email'])->firstOrFail();
        $business->members()->syncWithoutDetaching([$user->id => ['role' => $data['role'], 'joined_at' => now()]]);
        return back()->with('success', 'Miembro asociado al emprendimiento.');
    }

    /** Corta con 403 si el usuario actual no puede gestionar este emprendimiento. */
    private function ownerOnly(Business $business): void
    {
        abort_unless($business->canBeManagedBy(Auth::user()), 403);
    }

    /**
     * Calcula los horarios de inicio libres de un día según el horario de atención (por defecto 9 a 18),
     * la duración del turno y los turnos ya reservados no cancelados.
     */
    private function availableSlotTimes(Business $business, Carbon $date, ?int $excludeAppointmentId = null): array
    {
        $day = strtolower($date->format('l'));
        $hours = $business->availabilityHours()->where('day_of_week', $day)->first();

        if (!$hours && $business->appointments_enabled) {
            $hours = (object) ['opening_time' => '09:00', 'closing_time' => '18:00', 'is_closed' => false];
        }

        if (!$hours || $hours->is_closed || !$hours->opening_time || !$hours->closing_time) {
            return [];
        }

        $duration = $business->appointment_slot_duration ?? 30;
        $opening = Carbon::parse($hours->opening_time);
        $closing = Carbon::parse($hours->closing_time);
        $booked = $business->appointments()
            ->whereDate('appointment_date', $date)
            ->when($excludeAppointmentId, fn ($query) => $query->where('id', '!=', $excludeAppointmentId))
            ->whereNotIn('status', ['cancelled'])
            ->get(['start_time', 'end_time']);
        $slots = [];

        for ($slot = $opening->copy(); $slot->copy()->addMinutes($duration)->lte($closing); $slot->addMinutes($duration)) {
            if ($date->isToday() && $slot->lessThanOrEqualTo(now())) {
                continue;
            }

            $slotStart = $slot->format('H:i');
            $slotEnd = $slot->copy()->addMinutes($duration);
            // Dos turnos se pisan si sus rangos se solapan.
            $isBooked = $booked->contains(function ($appointment) use ($slot, $slotEnd) {
                $bookedStart = Carbon::parse($appointment->start_time);
                $bookedEnd = $appointment->end_time
                    ? Carbon::parse($appointment->end_time)
                    : $bookedStart->copy()->addMinutes(30);

                return $slot->lt($bookedEnd) && $slotEnd->gt($bookedStart);
            });

            if (!$isBooked) {
                $slots[] = $slotStart;
            }
        }

        return $slots;
    }
}
