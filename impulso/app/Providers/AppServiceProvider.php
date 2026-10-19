<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Business;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\User;
use App\Services\Sms\LogSmsSender;
use App\Services\Sms\SmsSender;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
        // Para usar un proveedor real, registrar aquí otra implementación de SmsSender.
        $this->app->bind(SmsSender::class, LogSmsSender::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $user = Auth::user();
            if (!$user instanceof User) {
                $view->with(['globalInbox' => null, 'hasImpulsoPlusAccess' => false]);
                return;
            }

            $cache = Cache::store('file');
            $cacheNamespace = app()->environment().':'.$user->id;
            $hasImpulsoPlusAccess = $cache->remember('layout:plus-access:v1:'.$cacheNamespace, now()->addSeconds(30), fn () =>
                $user->role === 'superadmin'
                || $user->ownedBusinesses()->exists()
                || $user->businesses()->wherePivotIn('role', ['owner', 'administrator'])->exists()
            );
            $view->with('hasImpulsoPlusAccess', $hasImpulsoPlusAccess);

            $inboxCacheKey = 'layout:global-inbox:v1:'.$cacheNamespace;
            $cachedInbox = $cache->get($inboxCacheKey);
            if ($cachedInbox !== null) {
                $view->with('globalInbox', $cachedInbox);
                return;
            }

            $managedBusinesses = Business::query()
                ->select(['id', 'name', 'slug'])
                ->where('owner_id', $user->id)
                ->orWhereHas('members', fn ($query) => $query
                    ->whereKey($user->id)
                    ->whereIn('business_members.role', ['owner', 'administrator']))
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
                : Inquiry::query()
                    ->where('client_id', $user->id)
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
                : Inquiry::query()
                    ->where('client_id', $user->id)
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

            $globalInbox = [
                'title' => $managesBusinesses ? 'Mensajes' : 'Mi bandeja',
                'subtitle' => $managesBusinesses ? 'Actividad de tus emprendimientos' : 'Consultas y respuestas',
                'messages' => $messages,
                'notifications' => $notifications,
                'message_tab' => $managesBusinesses ? 'Consultas' : 'Enviadas',
                'notification_tab' => 'Notificaciones',
                'total' => $messages->count() + $notifications->count(),
            ];
            $cache->put($inboxCacheKey, $globalInbox, now()->addSeconds(20));
            $view->with('globalInbox', $globalInbox);
        });
    }
}
