@extends('layouts.app')
@section('content')
<section class="form-page wrap">
  <div class="dashboard-top">
    <div>
      <p class="eyebrow">Perfil público de {{ $business->name }}</p>
      <h1>Personalizá tu página.</h1>
      <p class="muted">Elegí una combinación preparada para que todo siga siendo fácil de leer.</p>
    </div>
    <a class="button secondary" href="{{ route('business.show', $business) }}">Ver perfil <span>↗</span></a>
  </div>

  <form method="POST" action="{{ route('business.customization.update', $business) }}" class="customization-form">
    @csrf
    <fieldset>
      <legend>Paleta de colores</legend>
      <div class="customization-options palette-options">
        @foreach($palettes as $key => $palette)
          <label class="customization-option">
            <input type="radio" name="public_palette" value="{{ $key }}" @checked(($business->public_palette ?? 'mint') === $key)>
            <span class="palette-preview" style="--preview-accent:{{ $palette['accent'] }}; --preview-soft:{{ $palette['soft'] }}; --preview-ink:{{ $palette['ink'] }}"><i></i><b>{{ $palette['label'] }}</b></span>
          </label>
        @endforeach
      </div>
    </fieldset>

    <fieldset>
      <legend>Fondo</legend>
      <div class="customization-options background-options">
        @foreach($backgrounds as $key => $label)
          <label class="customization-option">
            <input type="radio" name="public_background" value="{{ $key }}" @checked(($business->public_background ?? 'plain') === $key)>
            <span class="background-preview background-preview-{{ $key }}"><b>{{ $label }}</b></span>
          </label>
        @endforeach
      </div>
    </fieldset>

    <button class="button" type="submit">Guardar personalización <span>→</span></button>
  </form>
</section>
@endsection