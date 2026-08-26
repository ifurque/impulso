@extends('layouts.app')
@section('content')
<section class="dashboard wrap">
  <div class="dashboard-top">
    <div>
      <p class="eyebrow">Tu espacio de trabajo</p>
      <h1>Elegí un emprendimiento.</h1>
      <p class="muted">Seleccioná cuál querés administrar.</p>
    </div>
    <a class="button" href="{{ route('business.create') }}">Crear emprendimiento <span>+</span></a>
  </div>

  <div class="business-panel-grid">
    @forelse($businesses as $business)
      <a class="business-panel-card" href="{{ route('dashboard', $business) }}">
        <div class="business-panel-logo">
          @if($business->profile_photo)
            <img src="{{ asset('storage/'.$business->profile_photo) }}" alt="Logo de {{ $business->name }}">
          @else
            {{ Str::substr($business->name, 0, 1) }}
          @endif
        </div>
        <div>
          <p class="eyebrow">{{ $business->category }}</p>
          <h2>{{ $business->name }}</h2>
          <p class="muted">{{ $business->location }}</p>
          <small class="muted">{{ $business->appointments_enabled ? 'Agenda activa' : 'Agenda desactivada' }} · {{ $business->pending_appointments_count }} turnos pendientes</small>
        </div>
        <span class="business-panel-arrow">→</span>
      </a>
    @empty
      <div class="panel">
        <h2>Todavía no tenés emprendimientos.</h2>
        <a class="button" href="{{ route('business.create') }}">Crear emprendimiento <span>→</span></a>
      </div>
    @endforelse
  </div>
</section>
<style>
  .business-panel-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; }
  .business-panel-card { display: grid; grid-template-columns: auto 1fr auto; align-items: center; gap: 16px; padding: 22px; border: 1px solid var(--line); background: var(--paper); color: inherit; text-decoration: none; transition: border-color .2s, transform .2s; }
  .business-panel-card:hover { border-color: var(--ink); transform: translateY(-2px); }
  .business-panel-logo { width: 54px; height: 54px; display: grid; place-items: center; overflow: hidden; background: var(--accent); color: var(--ink); font-size: 1.4rem; font-weight: 700; }
  .business-panel-logo img { width: 100%; height: 100%; object-fit: cover; }
  .business-panel-card .eyebrow { margin-bottom: 4px; }
  .business-panel-card h2 { margin: 0; font-size: 1.15rem; }
  .business-panel-card p { margin: 0; }
  .business-panel-arrow { font-size: 1.4rem; }
  @media (max-width: 560px) { .dashboard-top { align-items: start; gap: 18px; } .business-panel-card { grid-template-columns: auto 1fr; } .business-panel-arrow { grid-column: 2; } }
</style>
@endsection