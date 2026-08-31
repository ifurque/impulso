@extends('layouts.app')
@section('content')
<section class="dashboard wrap"><div class="dashboard-top"><div><p class="eyebrow">Panel de {{ $business->name }}</p><h1>Hola, {{ Str::before(auth()->user()->name, ' ') }}.</h1><p class="muted">Aquí tienes una mirada rápida de lo que está pasando.</p><a class="button create-post-action" href="{{ route('posts.create', $business) }}">Crear publicación <span>+</span></a></div><a class="button secondary" href="{{ route('business.show', $business) }}">Ver perfil público <span>↗</span></a></div>
<div class="metric-grid"><div class="metric-card accent"><span>Pagos entrantes · 30 días</span><strong>$ {{ number_format($totalIncomes, 0, ',', '.') }}</strong><small>Todo lo que ingresó a tu negocio.</small></div><div class="metric-card"><span>Gastos · 30 días</span><strong>$ {{ number_format($totalExpenses, 0, ',', '.') }}</strong><small>Movimientos registrados</small></div>@if($business->appointments_enabled)<div class="metric-card"><span>Turnos próximos</span><strong>{{ $appointments }}</strong><small class="positive">● En seguimiento</small></div>@endif @if($business->delivery_enabled)<div class="metric-card"><span>Pedidos pendientes</span><strong>{{ $pendingOrders ?? 0 }}</strong><small class="positive">● Revisión de envíos</small></div>@endif</div>
<div class="dashboard-grid">
  <div class="dashboard-main">
    <div class="panel">
      <div class="panel-head">
        <div>
          <p class="eyebrow">Egresos</p>
          <h2>Gastos por categoría</h2>
        </div>
        <a class="text-link" href="{{ route('movements.index', $business) }}">Ver movimientos →</a>
      </div>
      @if($categoryTotals->count())
        <div class="bars">
          @foreach($categoryTotals as $category => $amount)
            <div class="bar-row">
              <div>
                <span>{{ $category }}</span>
                <strong>$ {{ number_format($amount, 0, ',', '.') }}</strong>
              </div>
              <div class="bar-track">
                <i style="width: {{ max(8, ($amount / max(1, $categoryTotals->first())) * 100) }}%"></i>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <p class="muted">Todavía no hay gastos registrados.</p>
      @endif
    </div>

    <div class="panel">
      <div class="panel-head">
        <div>
          <p class="eyebrow">Ingresos</p>
          <h2>Pagos recibidos</h2>
        </div>
        <a class="text-link" href="{{ route('movements.index', $business) }}">Ver movimientos →</a>
      </div>
      @if($incomeTotals->count())
        <div class="bars">
          @foreach($incomeTotals as $source => $amount)
            <div class="bar-row">
              <div>
                <span>{{ $source === 'service' ? 'Servicios' : ($source === 'sale' ? 'Ventas' : 'Otros') }}</span>
                <strong>$ {{ number_format($amount, 0, ',', '.') }}</strong>
              </div>
              <div class="bar-track income">
                <i style="width: {{ max(8, ($amount / max(1, $incomeTotals->first())) * 100) }}%"></i>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <p class="muted">Todavía no hay pagos entrantes.</p>
      @endif
    </div>
  </div>

  <div class="panel quick-panel">
    <p class="eyebrow">Acciones rápidas</p>
    <h2>Haz avanzar tu día.</h2>
    @if($business->delivery_enabled && ($pendingOrders ?? 0) > 0)
      <div class="notice success-box" style="margin-bottom: 16px;">Tienes {{ $pendingOrders }} pedido(s) pendiente(s) con envío.</div>
    @endif
    <div class="quick-links">
      <a href="{{ route('movements.index', $business) }}" class="quick-link">
        <span>↕</span>
        <div>
          <strong>Ver movimientos</strong>
          <small>Gastos e ingresos</small>
        </div>
      </a>
      @if($business->appointments_enabled)<a href="{{ route('management.index', $business) }}" class="quick-link">
        <span>◷</span>
        <div>
          <strong>Ver turnos</strong>
          <small>Gestiona citas y consultas</small>
        </div>
      </a>
      @endif
      <a href="{{ route('posts.index', $business) }}" class="quick-link">
        <span>✦</span>
        <div>
          <strong>Publicaciones</strong>
          <small>Administra contenido</small>
        </div>
      </a>
      <a href="{{ route('members.index', $business) }}" class="quick-link">
        <span>⌁</span>
        <div>
          <strong>Equipo y redes</strong>
          <small>Conexiones y miembros</small>
        </div>
      </a>
      <a href="{{ route('availability.index', $business) }}" class="quick-link">
        <span>🕐</span>
        <div>
          <strong>Horarios</strong>
          <small>Configure días y horarios</small>
        </div>
      </a>
      <a href="{{ route('business.customization', $business) }}" class="quick-link">
        <span>◈</span>
        <div>
          <strong>Personalizar página</strong>
          <small>Colores y fondos del perfil público</small>
        </div>
      </a>
    </div>
  </div>
</div>
<details class="business-inbox">
  <summary aria-label="Abrir bandeja de consultas y notificaciones">
    <span aria-hidden="true">▰</span>
    @if($inboxInquiries->isNotEmpty() || $notifications->isNotEmpty())<b>{{ $inboxInquiries->count() + $notifications->count() }}</b>@endif
  </summary>
  <div class="business-inbox-menu">
    <div class="business-inbox-title"><div><strong>Mensajes</strong><span>Actividad de {{ $business->name }}</span></div><span class="inbox-close">×</span></div>
    <div class="inbox-tabs" role="tablist"><button class="inbox-tab is-active" type="button" data-inbox-tab="messages">Consultas <b>{{ $inboxInquiries->count() }}</b></button><button class="inbox-tab" type="button" data-inbox-tab="notifications">Notificaciones <b>{{ $notifications->count() }}</b></button></div>
    <div class="inbox-pane is-active" data-inbox-pane="messages">
      @forelse($inboxInquiries as $inquiry)
        <a href="{{ route('management.index', $business) }}" class="inbox-conversation"><span class="inbox-avatar">{{ Str::upper(Str::substr($inquiry->client?->name ?? 'C', 0, 1)) }}</span><span><strong>{{ $inquiry->client?->name ?? 'Cliente' }}</strong><small>{{ $inquiry->subject }}</small><em>{{ Str::limit($inquiry->message, 62) }}</em></span><i>Nuevo</i></a>
      @empty
        <div class="inbox-empty"><strong>Tu bandeja está al día.</strong><span>Las consultas de clientes aparecerán acá.</span></div>
      @endforelse
    </div>
    <div class="inbox-pane" data-inbox-pane="notifications">
      @forelse($notifications as $notification)
        <a href="{{ route('management.index', $business) }}" class="inbox-notification">
          <span class="notification-icon">{{ $notification instanceof \App\Models\Appointment ? '◷' : '□' }}</span>
          <span>@if($notification instanceof \App\Models\Appointment)<strong>Turno pendiente</strong><small>{{ $notification->client?->name ?? $notification->guest_name }} · {{ \Carbon\Carbon::parse($notification->appointment_date)->format('d/m') }} a las {{ $notification->start_time }}</small>@else<strong>Pedido pendiente</strong><small>{{ $notification->customer_name }} · $ {{ number_format($notification->total, 0, ',', '.') }}</small>@endif</span>
        </a>
      @empty
        <div class="inbox-empty"><strong>No hay novedades.</strong><span>Los turnos y pedidos pendientes aparecerán acá.</span></div>
      @endforelse
    </div>
  </div>
</details>
</section>
<script>
  document.querySelectorAll('.inbox-tab').forEach((tab) => tab.addEventListener('click', () => {
    document.querySelectorAll('.inbox-tab, .inbox-pane').forEach((item) => item.classList.remove('is-active'));
    tab.classList.add('is-active');
    document.querySelector(`[data-inbox-pane="${tab.dataset.inboxTab}"]`).classList.add('is-active');
  }));
</script>
@endsection