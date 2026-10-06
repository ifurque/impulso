@extends('layouts.app')
@section('content')
<section class="dashboard wrap">
  @include('settings._tabs', ['active' => 'businesses'])
  <div class="dashboard-top">
    <div>
      <p class="eyebrow">Tu espacio de trabajo</p>
      <h1>Elegí un emprendimiento.</h1>
      <p class="muted">Seleccioná cuál querés administrar.</p>
    </div>
    <a class="button" href="{{ route('business.create') }}">Crear emprendimiento <span>+</span></a>
  </div>

  <div class="admin-list business-owner-list">
    @forelse($businesses as $business)
      <article class="admin-row">
        <span class="admin-avatar">
          @if($business->profile_photo_url)
            <img src="{{ $business->profile_photo_url }}" alt="Logo de {{ $business->name }}">
          @else
            {{ Str::substr($business->name, 0, 1) }}
          @endif
        </span>
        <div class="admin-copy">
          <div class="admin-name">
            <strong>{{ $business->name }}</strong>
            @if($business->is_plus)<span class="verified-badge" aria-label="Verificado por +Impulso" title="Verificado por +Impulso">✓</span>@endif
          </div>
          <p class="admin-meta">{{ $business->location ?: 'Sin zona' }} <span>·</span> {{ $business->category ?: 'Sin clasificación' }}</p>
          <p class="admin-submeta">{{ $business->email ?: 'Sin correo de contacto' }} <span>·</span> {{ $business->phone ?: 'Sin teléfono' }} <span>·</span> {{ $business->appointments_enabled ? 'Agenda activa' : 'Agenda desactivada' }} · {{ $business->pending_appointments_count }} turnos pendientes</p>
        </div>
        <div class="admin-actions">
          <a class="button secondary button-small" href="{{ route('dashboard', $business) }}">Administrar</a>
          <a class="button secondary button-small" href="{{ route('business.show', $business) }}">Ver perfil</a>
        </div>
      </article>
    @empty
      <div class="panel">
        <h2>Todavía no tenés emprendimientos.</h2>
        <a class="button" href="{{ route('business.create') }}">Crear emprendimiento <span>→</span></a>
      </div>
    @endforelse
  </div>
</section>
@endsection