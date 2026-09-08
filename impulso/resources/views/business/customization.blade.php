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
      <legend>Presets rápidos del perfil público</legend>
      <div class="preset-grid" id="business-preset-grid">
        <button type="button" class="preset-card" data-palette="mint" data-background="plain" data-nav="#e3f3e9" data-post="#e3f3e9">
          <strong>Minimal</strong>
          <small>Fresco y limpio para lectura rápida.</small>
        </button>
        <button type="button" class="preset-card" data-palette="sun" data-background="paper" data-nav="#fff0cf" data-post="#ffe8bf">
          <strong>Tierra</strong>
          <small>Cálido y cercano para tiendas.</small>
        </button>
        <button type="button" class="preset-card" data-palette="ocean" data-background="grid" data-nav="#d9edf4" data-post="#d8e8ee">
          <strong>Océano</strong>
          <small>Profesional con acento azul.</small>
        </button>
        <button type="button" class="preset-card" data-palette="coral" data-background="dots" data-nav="#ffe7e5" data-post="#f7d7d9">
          <strong>Coral</strong>
          <small>Activo y enérgico para ofertas.</small>
        </button>
      </div>
    </fieldset>
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

    <fieldset>
      <legend>Colores extra del perfil público</legend>
      <div class="form-inline">
        <label>
          Color de navbar
          <input class="color-bar-input" type="color" name="public_navbar_color" value="{{ $business->public_navbar_color ?? '#eef1ea' }}">
        </label>
        <label>
          Fondo de publicaciones
          <input class="color-bar-input" type="color" name="public_posts_background" value="{{ $business->public_posts_background ?? '#e8e6b6' }}">
        </label>
      </div>
      <p class="muted" style="margin-top:8px;">Si no elegís color, se usa la paleta seleccionada.</p>
    </fieldset>

    <button class="button" type="submit">Guardar personalización <span>→</span></button>
  </form>
</section>

<script>
  const businessPresetGrid = document.getElementById('business-preset-grid');
  businessPresetGrid?.querySelectorAll('.preset-card').forEach((card) => {
    card.addEventListener('click', () => {
      const { palette, background, nav, post } = card.dataset;

      const paletteInput = document.querySelector(`input[name="public_palette"][value="${palette}"]`);
      const backgroundInput = document.querySelector(`input[name="public_background"][value="${background}"]`);
      const navInput = document.querySelector('input[name="public_navbar_color"]');
      const postInput = document.querySelector('input[name="public_posts_background"]');

      if (paletteInput) paletteInput.checked = true;
      if (backgroundInput) backgroundInput.checked = true;
      if (navInput && nav) navInput.value = nav;
      if (postInput && post) postInput.value = post;
    });
  });
</script>
@endsection