@extends('layouts.app')
@section('content')
<section class="form-page wrap">
  <div class="section-heading">
    <p class="eyebrow">Gestionar turno</p>
    <h1>{{ $appointment->business->name }}</h1>
    <p class="muted">{{ $appointment->appointment_date->format('d/m/Y') }} a las {{ $appointment->start_time }}</p>
  </div>
  <div class="panel">
    @if($appointment->status === 'cancelled')
      <p class="muted">Este turno está cancelado.</p>
    @else
      <form method="POST" action="{{ route('appointments.reschedule', $appointment->management_token) }}" class="form">
        @csrf
        <label> nueva fecha <input type="date" name="appointment_date" min="{{ now()->format('Y-m-d') }}" value="{{ $appointment->appointment_date->format('Y-m-d') }}" required></label>
        <label>Nuevo horario <input type="time" name="start_time" value="{{ $appointment->start_time }}" required></label>
        <button class="button full">Reprogramar turno</button>
      </form>
      <form method="POST" action="{{ route('appointments.cancel', $appointment->management_token) }}" style="margin-top: 12px;">
        @csrf
        <button class="button secondary full">Cancelar turno</button>
      </form>
    @endif
  </div>
</section>
@endsection