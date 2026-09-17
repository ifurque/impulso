<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Inquiry;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $user = Auth::user();
            if (!$user) {
                $view->with('globalInbox', null);
                return;
            }

            $managedBusinesses = Business::query()
                ->select(['id', 'name', 'slug'])
                ->where('owner_id', $user->id)
                ->orWhereHas('members', fn ($query) => $query->whereKey($user->id)->wherePivotIn('role', ['owner', 'administrator']))
                ->get();

            $managedIds = $managedBusinesses->pluck('id');
            $managesBusinesses = $managedIds->isNotEmpty();

            $messages = $managesBusinesses
                ? Inquiry::query()
                    ->with(['client:id,name', 'business:id,name,slug'])
                    ->whereIn('business_id', $managedIds)
                    ->where('status', 'pending')
                    ->latest()
                    ->limit(8)
                    ->get()
                    ->map(fn ($inquiry) => [
                        'avatar' => Str::upper(Str::substr($inquiry->client?->name ?? 'C', 0, 1)),
                        'title' => $inquiry->client?->name ?? 'Cliente',
                        'subtitle' => ($inquiry->business?->name ?? 'Emprendimiento').' · '.$inquiry->subject,
                        'excerpt' => Str::limit($inquiry->message, 72),
                        'href' => $inquiry->business ? route('management.index', $inquiry->business) : route('business.panels'),
                        'badge' => 'Nuevo',
                    ])
                : $user->inquiries()
                    ->with('business:id,name,slug')
                    ->latest()
                    ->limit(8)
                    ->get()
                    ->map(fn ($inquiry) => [
                        'avatar' => Str::upper(Str::substr($inquiry->business?->name ?? 'E', 0, 1)),
                        'title' => $inquiry->business?->name ?? 'Emprendimiento',
                        'subtitle' => $inquiry->subject,
                        'excerpt' => Str::limit($inquiry->message, 72),
                        'href' => route('inbox.index'),
                        'badge' => $inquiry->status === 'answered' ? 'Respondida' : 'Enviada',
                    ]);

            $notifications = $managesBusinesses
                ? Appointment::query()
                    ->with(['client:id,name', 'business:id,name,slug'])
                    ->whereIn('business_id', $managedIds)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->latest('appointment_date')
                    ->limit(4)
                    ->get()
                    ->map(fn ($appointment) => [
                        'icon' => '◷',
                        'title' => 'Turno pendiente',
                        'subtitle' => ($appointment->business?->name ?? 'Emprendimiento').' · '.($appointment->client?->name ?? $appointment->guest_name),
                        'excerpt' => optional($appointment->appointment_date)->format('d/m').' a las '.$appointment->start_time,
                        'href' => $appointment->business ? route('management.index', $appointment->business) : route('business.panels'),
                        'created_at' => $appointment->created_at,
                    ])
                    ->concat(
                        Order::query()
                            ->with('business:id,name,slug')
                            ->whereIn('business_id', $managedIds)
                            ->where('status', 'pending')
                            ->latest()
                            ->limit(4)
                            ->get()
                            ->map(fn ($order) => [
                                'icon' => '□',
                                'title' => 'Pedido pendiente',
                                'subtitle' => ($order->business?->name ?? 'Emprendimiento').' · '.$order->customer_name,
                                'excerpt' => '$ '.number_format($order->total ?? 0, 0, ',', '.'),
                                'href' => $order->business ? route('management.index', $order->business) : route('business.panels'),
                                'created_at' => $order->created_at,
                            ])
                    )
                    ->sortByDesc('created_at')
                    ->values()
                    ->take(8)
                : $user->inquiries()
                    ->with('business:id,name,slug')
                    ->whereNotNull('response')
                    ->latest('answered_at')
                    ->limit(8)
                    ->get()
                    ->map(fn ($inquiry) => [
                        'icon' => '✓',
                        'title' => 'Consulta respondida',
                        'subtitle' => $inquiry->business?->name ?? 'Emprendimiento',
                        'excerpt' => Str::limit($inquiry->response, 72),
                        'href' => route('inbox.index'),
                        'created_at' => $inquiry->answered_at ?? $inquiry->updated_at,
                    ]);

            $view->with('globalInbox', [
                'title' => $managesBusinesses ? 'Mensajes' : 'Mi bandeja',
                'subtitle' => $managesBusinesses ? 'Actividad de tus emprendimientos' : 'Consultas y respuestas',
                'messages' => $messages,
                'notifications' => $notifications,
                'message_tab' => $managesBusinesses ? 'Consultas' : 'Enviadas',
                'notification_tab' => 'Notificaciones',
                'total' => $messages->count() + $notifications->count(),
            ]);
        });
    }
}
