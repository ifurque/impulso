@extends('layouts.app')

@section('content')
<section class="plus-page wrap">
  <a class="back" href="{{ route('business.panels') }}">← Volver a mis emprendimientos</a>
  <div class="plus-intro">
    <p class="eyebrow">Membresía mensual</p>
    <h1>Más impulso para tu marca.</h1>
    <p class="muted">Elegí el emprendimiento para conocer los beneficios de Impulso +.</p>
  </div>

  <div class="plus-business-list">
    @foreach($businesses as $business)
      <a class="plus-business-card" href="{{ route('business.plus.show', $business) }}">
        <span class="plus-business-mark">{{ Str::substr($business->name, 0, 1) }}</span>
        <span>
          <small>{{ $business->category }}</small>
          <strong>{{ $business->name }}</strong>
        </span>
        <span class="plus-arrow">→</span>
      </a>
    @endforeach
  </div>
</section>
@endsection
