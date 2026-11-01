@extends('layouts.app')
{{-- Vista: selector de "Mis emprendimientos" del usuario para entrar a administrarlos --}}
@section('content')
{{-- Botón de guía interactiva con los pasos del tour de ayuda --}}
@include('partials.help-tour-trigger', ['label' => 'Guía de mis emprendimientos', 'steps' => [
  ['target' => '.dashboard-top .button', 'title' => 'Crear emprendimiento', 'body' => 'Registra un nuevo emprendimiento en pocos pasos.'],
  ['target' => '.business-owner-list .admin-row', 'title' => 'Tus emprendimientos', 'body' => 'Cada fila resume zona, rubro, contacto y si la agenda está activa.'],
  ['target' => '.business-owner-list .admin-actions > a:first-child', 'title' => 'Administrar', 'body' => 'Entra al panel para gestionar publicaciones, turnos, movimientos y personalización.'],
  ['target' => '.business-owner-list .admin-actions > a:last-child', 'title' => 'Ver perfil', 'body' => 'Mira cómo ve tu emprendimiento el público.'],
]])
<section class="dashboard wrap">
  {{-- Pestañas de "Mi perfil" --}}
  @include('settings._tabs', ['active' => 'businesses'])
  {{-- Encabezado con acceso para crear un emprendimiento --}}
  <div class="dashboard-top">
    <div>
      <p class="eyebrow">Tu espacio de trabajo</p>
      <h1>Elegí un emprendimiento.</h1>
      <p class="muted">Seleccioná cuál querés administrar.</p>
    </div>
    <a class="button" href="{{ route('business.create') }}">Crear emprendimiento <span>+</span></a>
  </div>

  {{-- Listado de emprendimientos propios --}}
  <div class="admin-list business-owner-list">
    @forelse($businesses as $business)
      {{-- Fila: logo, datos del negocio (incluye estado de la agenda) y acciones --}}
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
      {{-- Estado vacío: invita a crear el primer emprendimiento --}}
      <div class="panel">
        <h2>Todavía no tenés emprendimientos.</h2>
        <a class="button" href="{{ route('business.create') }}">Crear emprendimiento <span>→</span></a>
      </div>
    @endforelse
  </div>
</section>
@endsection