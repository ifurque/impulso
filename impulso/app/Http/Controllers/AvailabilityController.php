<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessAvailabilityHours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    public function index(Business $business)
    {
        abort_unless($business->canBeManagedBy(Auth::user()), 403);

        $days = ['monday' => 'Lunes', 'tuesday' => 'Martes', 'wednesday' => 'Miércoles', 'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado', 'sunday' => 'Domingo'];
        
        $availability = $business->availabilityHours()->get()->keyBy('day_of_week');

        // Create default availability for all days
        foreach ($days as $day => $label) {
            if (!$availability->has($day)) {
                $availability[$day] = new BusinessAvailabilityHours([
                    'day_of_week' => $day,
                    'opening_time' => '09:00',
                    'closing_time' => '18:00',
                    'is_closed' => false,
                ]);
            }
        }

        return view('business.availability', [
            'business' => $business,
            'days' => $days,
            'availability' => $availability,
        ]);
    }

    public function update(Request $request, Business $business)
    {
        abort_unless($business->canBeManagedBy(Auth::user()), 403);

        $data = $request->validate([
            'appointments_enabled' => 'boolean',
            'delivery_enabled' => 'boolean',
            'delivery_radius_km' => 'nullable|integer|min:0|max:50',
            'delivery_cost' => 'nullable|integer|min:0',
            'delivery_latitude' => 'nullable|numeric|between:-90,90',
            'delivery_longitude' => 'nullable|numeric|between:-180,180',
            'customer_payment_methods' => 'nullable|array',
            'customer_payment_methods.*' => 'string|in:efectivo,transferencia,tarjeta,mercado_pago,qr,cuenta_corriente',
            'business_payment_methods' => 'nullable|array',
            'business_payment_methods.*' => 'string|in:transferencia,mercado_pago,efectivo,tarjeta,qr',
            'appointment_slot_duration' => 'nullable|in:15,30,60',
            'hours.*.day_of_week' => 'required|string',
            'hours.*.is_closed' => 'boolean',
            'hours.*.opening_time' => 'nullable|date_format:H:i',
            'hours.*.closing_time' => 'nullable|date_format:H:i',
        ]);

        $appointmentsEnabled = (bool) ($data['appointments_enabled'] ?? false);
        $deliveryEnabled = (bool) ($data['delivery_enabled'] ?? false);

        foreach ($data['hours'] ?? [] as $hours) {
            if ($appointmentsEnabled && !($hours['is_closed'] ?? false) && !empty($hours['opening_time']) && !empty($hours['closing_time']) && $hours['opening_time'] >= $hours['closing_time']) {
                return back()->withErrors(['hours' => 'La hora de cierre debe ser posterior a la de apertura.'])->withInput();
            }
        }

        $business->update([
            'appointments_enabled' => $appointmentsEnabled,
            'delivery_enabled' => $deliveryEnabled,
            'delivery_radius_km' => $deliveryEnabled ? (int) ($data['delivery_radius_km'] ?? 0) : 0,
            'delivery_cost' => $deliveryEnabled ? (int) ($data['delivery_cost'] ?? 0) : 0,
            'delivery_latitude' => $deliveryEnabled ? ($data['delivery_latitude'] ?? null) : null,
            'delivery_longitude' => $deliveryEnabled ? ($data['delivery_longitude'] ?? null) : null,
            'payment_methods_customer' => $deliveryEnabled ? ($data['customer_payment_methods'] ?? []) : [],
            'payment_methods_business' => $deliveryEnabled ? ($data['business_payment_methods'] ?? []) : [],
            'appointment_slot_duration' => $appointmentsEnabled ? (int) ($data['appointment_slot_duration'] ?? 30) : 30,
        ]);

        foreach ($data['hours'] as $hours) {
            $is_closed = $appointmentsEnabled ? ($hours['is_closed'] ?? false) : true;
            
            BusinessAvailabilityHours::updateOrCreate(
                ['business_id' => $business->id, 'day_of_week' => $hours['day_of_week']],
                [
                    'opening_time' => $is_closed ? null : ($hours['opening_time'] ?? null),
                    'closing_time' => $is_closed ? null : ($hours['closing_time'] ?? null),
                    'is_closed' => $is_closed,
                ]
            );
        }

        return redirect()->route('availability.index', $business)->with('success', 'Horarios actualizados correctamente.');
    }
}
