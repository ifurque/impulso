@extends('layouts.app')

{{-- Gestión de miembros del equipo del emprendimiento --}}
@section('content')
<section class="dashboard wrap">
  {{-- Encabezado con título y botón para volver al panel --}}
  <div class="dashboard-top"><div><p class="eyebrow">Equipo de {{ $business->name }}</p><h1>Miembros.</h1><p class="muted">Administrá quién puede trabajar en tu emprendimiento.</p></div><a class="button secondary" href="{{ route('dashboard', $business) }}">Volver al panel</a></div>
  {{-- Grilla de tarjetas: propietario, miembros actuales y formulario para sumar uno nuevo --}}
  <div class="member-card-grid">
    {{-- Tarjeta del propietario --}}
    <article class="member-card owner-card"><span class="member-avatar">{{ Str::upper(Str::substr($business->owner->name, 0, 1)) }}</span><div><p class="eyebrow">Propietario</p><h2>{{ $business->owner->name }}</h2><p class="muted">{{ $business->owner->email }}</p></div></article>
    {{-- Tarjetas de los miembros con su rol --}}
    @foreach($business->members as $member)
      <article class="member-card"><span class="member-avatar">{{ Str::upper(Str::substr($member->name, 0, 1)) }}</span><div><p class="eyebrow">{{ $member->pivot->role === 'administrator' ? 'Administrador' : 'Empleado' }}</p><h2>{{ $member->name }}</h2><p class="muted">{{ $member->email }}</p></div></article>
    @endforeach
    {{-- Formulario para agregar un miembro por correo y rol --}}
    <article class="member-card add-member-card"><h2>Sumar miembro</h2><form method="POST" action="{{ route('members.store', $business) }}" class="form">@csrf<label>Correo de usuario<input name="email" type="email" required placeholder="persona@correo.com"></label><label>Rol<input name="role" type="text" required maxlength="60" placeholder="Ej. Administrador"></label><button class="button">Agregar <span>+</span></button></form></article>
  </div>
</section>
@endsection
