@extends('layouts.app')

@section('content')
<section class="dashboard wrap">
  <div class="dashboard-top">
    <div><p class="eyebrow">Administración</p><h1>Gestion operativa.</h1><p class="muted">Administra envios, atencion y mensajes desde un solo lugar.</p></div>
    <div class="page-actions">
      <a class="button secondary" href="{{ route('management.database', $business) }}">Ir a base de datos</a>
      <a class="button secondary" href="{{ route('dashboard', $business) }}">Volver al panel</a>
    </div>
  </div>

  <div class="management-grid {{ $business->delivery_enabled ? 'has-delivery' : '' }}">
    <section class="panel management-panel">
      <p class="eyebrow">Sistema de envíos</p><h2>Zona de cobertura</h2>
      @if($business->delivery_enabled && $business->delivery_latitude !== null && $business->delivery_longitude !== null)
        <p class="muted">La comprobación automática está activa en un radio de <strong>{{ $business->delivery_radius_km }} km</strong> desde {{ $business->location }}.</p>
        <span class="notice success-box">Ubicación del local configurada</span>
      @elseif($business->delivery_enabled)
        <p class="muted">Falta guardar la ubicación exacta del local para que el sistema pueda decirle al cliente si está dentro del rango.</p>
        <span class="notice error-box">Cobertura automática pendiente</span>
      @else
        <p class="muted">Activa los envíos y define el radio desde el apartado de horarios.</p>
      @endif
      <a class="button secondary" href="{{ route('availability.index', $business) }}">Configurar sistema de envíos</a>
    </section>
    @if($business->delivery_enabled)
      <section class="panel management-panel">
        <p class="eyebrow">Pedidos con envío</p><h2>Entregas a domicilio</h2>
        <div class="stack-list">@forelse($deliveryOrders as $order)<div class="mini-item"><div><strong>{{ $order->customer_name }}</strong><small>{{ $order->product?->name ?? 'Producto' }} · {{ $order->quantity }} unidad(es)</small><small>{{ $order->delivery_address }}</small></div><div class="meta"><span>{{ ucfirst($order->payment_method) }}</span><strong>$ {{ number_format($order->total, 0, ',', '.') }}</strong></div><form method="POST" action="{{ route('orders.status', [$business, $order]) }}" class="inline-form">@csrf<select name="status"><option value="pending" @selected($order->status === 'pending')>Pendiente</option><option value="confirmed" @selected($order->status === 'confirmed')>Confirmado</option><option value="delivered" @selected($order->status === 'delivered')>Entregado</option><option value="cancelled" @selected($order->status === 'cancelled')>Cancelado</option></select><button class="plain-button">Guardar</button></form></div>@empty<p class="muted">Todavía no hay pedidos con envío.</p>@endforelse</div>
      </section>
    @endif

    <section class="panel management-panel">
      <p class="eyebrow">Pedidos sin envío</p><h2>Retiros en el emprendimiento</h2>
      <div class="stack-list">@forelse($pickupOrders as $order)<div class="mini-item"><div><strong>{{ $order->customer_name }}</strong><small>{{ $order->product?->name ?? 'Producto' }} · {{ $order->quantity }} unidad(es)</small><small>Retira en el emprendimiento</small></div><div class="meta"><span>{{ ucfirst($order->payment_method) }}</span><strong>$ {{ number_format($order->total, 0, ',', '.') }}</strong></div><form method="POST" action="{{ route('orders.status', [$business, $order]) }}" class="inline-form">@csrf<select name="status"><option value="pending" @selected($order->status === 'pending')>Pendiente</option><option value="confirmed" @selected($order->status === 'confirmed')>Confirmado</option><option value="delivered" @selected($order->status === 'delivered')>Retirado</option><option value="cancelled" @selected($order->status === 'cancelled')>Cancelado</option></select><button class="plain-button">Guardar</button></form></div>@empty<p class="muted">Todavía no hay pedidos para retirar.</p>@endforelse</div>
    </section>

    <section class="panel management-panel">
      <p class="eyebrow">Atención</p><h2>Turnos recientes</h2>
      <div class="expense-list">@forelse($appointments as $appointment)<div><span><strong>{{ $appointment->client?->name ?? $appointment->guest_name }} @if($appointment->product) · {{ $appointment->product->name }} @endif</strong><small>{{ $appointment->appointment_date->format('d/m/Y') }} a las {{ $appointment->start_time }} · {{ ucfirst($appointment->status) }}</small></span><form method="POST" action="{{ route('appointments.status', [$business, $appointment->id]) }}">@csrf<select name="status" onchange="this.form.submit()"><option value="pending" @selected($appointment->status === 'pending')>Pendiente</option><option value="confirmed" @selected($appointment->status === 'confirmed')>Confirmado</option><option value="completed" @selected($appointment->status === 'completed')>Completado</option><option value="cancelled" @selected($appointment->status === 'cancelled')>Cancelado</option></select></form></div>@empty<p class="muted">Todavía no hay turnos solicitados.</p>@endforelse</div>
    </section>

    <section class="panel management-panel">
      <p class="eyebrow">Mensajes</p><h2>Consultas de clientes</h2>
      <div class="stack-list">
        @forelse($inquiries as $inquiry)
          <article class="mini-item" style="display:block;">
            <div style="margin-bottom: 10px;">
              <strong>{{ $inquiry->client?->name ?? 'Cliente' }}</strong>
              <small>{{ $inquiry->subject }} · {{ $inquiry->created_at->format('d/m/Y H:i') }}</small>
              <small>{{ $inquiry->message }}</small>
            </div>
            <form method="POST" action="{{ route('inquiries.status', [$business, $inquiry->id]) }}" class="form" style="gap:10px;">
              @csrf
              <label>
                Respuesta
                <textarea name="response" rows="2" maxlength="1000" placeholder="Escribe una respuesta para el cliente">{{ old('response', $inquiry->response) }}</textarea>
              </label>
              <div class="inline-form">
                <select name="status">
                  <option value="pending" @selected($inquiry->status === 'pending')>Pendiente</option>
                  <option value="answered" @selected($inquiry->status === 'answered')>Respondida</option>
                  <option value="closed" @selected($inquiry->status === 'closed')>Cerrada</option>
                </select>
                <button class="plain-button">Guardar</button>
              </div>
            </form>
          </article>
        @empty
          <p class="muted">Todavía no hay consultas de clientes.</p>
        @endforelse
      </div>
    </section>
  </div>
</section>

<style>
  @media (max-width: 640px) {
    .management-panel .form .button { width: 100%; }
    .management-panel .mini-item { gap: 8px; padding: 12px 0; }
    .management-panel .mini-item form .plain-button { width: 100%; text-align: left; }
  }
</style>
@endsection
