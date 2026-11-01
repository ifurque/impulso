@extends('layouts.app')
{{-- Vista pública para que el cliente gestione su turno (reprogramar o cancelar) mediante un token --}}
@section('content')
<section class="form-page wrap">
  {{-- Encabezado: negocio y fecha/hora actual del turno --}}
  <div class="section-heading">
    <p class="eyebrow">Gestionar turno</p>
    <h1>{{ $appointment->business->name }}</h1>
    <p class="muted">{{ $appointment->appointment_date->format('d/m/Y') }} a las {{ $appointment->start_time }}</p>
  </div>
  <div class="panel">
    @if($appointment->status === 'cancelled')
      {{-- Turno ya cancelado: no hay acciones disponibles --}}
      <p class="muted">Este turno está cancelado.</p>
    @else
      {{-- Formulario para reprogramar: nueva fecha y horario --}}
      <form method="POST" action="{{ route('appointments.reschedule', $appointment->management_token) }}" class="form">
        @csrf
        <label> nueva fecha <input type="date" name="appointment_date" min="{{ now()->format('Y-m-d') }}" value="{{ $appointment->appointment_date->format('Y-m-d') }}" required></label>
        <label>Nuevo horario <input type="time" name="start_time" value="{{ $appointment->start_time }}" required></label>
        <button class="button full">Reprogramar turno</button>
      </form>
      {{-- Formulario para cancelar el turno --}}
      <form method="POST" action="{{ route('appointments.cancel', $appointment->management_token) }}" style="margin-top: 12px;">
        @csrf
        <button class="button secondary full">Cancelar turno</button>
      </form>
    @endif
  </div>
</section>
@endsection