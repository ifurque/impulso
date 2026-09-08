@extends('layouts.app')
@section('content')
<section class="form-page wrap">
  <div class="dashboard-top">
    <div>
      <p class="eyebrow">Tu cuenta</p>
      <h1>Personalizá tu experiencia.</h1>
      <p class="muted">Elegí fuente, modo visual y colores para toda la plataforma.</p>
    </div>
  </div>

  <form method="POST" action="{{ route('user.customization.update') }}" class="customization-form">
    @csrf

    <fieldset>
      <legend>Presets rápidos</legend>
      <div class="preset-grid" id="user-preset-grid">
        <button type="button" class="preset-card" data-font="dm" data-mode="day" data-bg="paper" data-nav="#eef1ea" data-text="#182522">
          <strong>Minimal</strong>
          <small>Limpio, claro y equilibrado.</small>
        </button>
        <button type="button" class="preset-card" data-font="serif" data-mode="day" data-bg="gradient" data-nav="#f3e9d2" data-text="#2c241a">
          <strong>Tierra</strong>
          <small>Cálido, editorial y elegante.</small>
        </button>
        <button type="button" class="preset-card" data-font="space" data-mode="night" data-bg="grid" data-nav="#172332" data-text="#e7f3ff">
          <strong>Neón Suave</strong>
          <small>Contraste moderno para modo noche.</small>
        </button>
        <button type="button" class="preset-card" data-font="mono" data-mode="day" data-bg="dots" data-nav="#dbe8e0" data-text="#10322b">
          <strong>Técnico</strong>
          <small>Preciso y legible para gestión diaria.</small>
        </button>
      </div>
    </fieldset>

    <fieldset>
      <legend>Tipografía</legend>
      <div class="customization-options">
        <label class="customization-option"><input type="radio" name="font_family" value="dm" @checked($preferences['font_family'] === 'dm')><span class="background-preview"><b>DM Sans (moderna)</b></span></label>
        <label class="customization-option"><input type="radio" name="font_family" value="space" @checked($preferences['font_family'] === 'space')><span class="background-preview"><b>Space Grotesk (impacto)</b></span></label>
        <label class="customization-option"><input type="radio" name="font_family" value="serif" @checked($preferences['font_family'] === 'serif')><span class="background-preview"><b>Merriweather (clásica)</b></span></label>
        <label class="customization-option"><input type="radio" name="font_family" value="mono" @checked($preferences['font_family'] === 'mono')><span class="background-preview"><b>JetBrains Mono (técnica)</b></span></label>
      </div>
    </fieldset>

    <fieldset>
      <legend>Modo</legend>
      <div class="customization-options">
        <label class="customization-option"><input type="radio" name="theme_mode" value="day" @checked($preferences['theme_mode'] === 'day')><span class="background-preview"><b>Modo día</b></span></label>
        <label class="customization-option"><input type="radio" name="theme_mode" value="night" @checked($preferences['theme_mode'] === 'night')><span class="background-preview"><b>Modo noche</b></span></label>
      </div>
    </fieldset>

    <fieldset>
      <legend>Fondo de pantalla</legend>
      <div class="customization-options">
        <label class="customization-option"><input type="radio" name="screen_background" value="paper" @checked($preferences['screen_background'] === 'paper')><span class="background-preview"><b>Papel</b></span></label>
        <label class="customization-option"><input type="radio" name="screen_background" value="soft" @checked($preferences['screen_background'] === 'soft')><span class="background-preview"><b>Suave</b></span></label>
        <label class="customization-option"><input type="radio" name="screen_background" value="grid" @checked($preferences['screen_background'] === 'grid')><span class="background-preview background-preview-grid"><b>Grilla</b></span></label>
        <label class="customization-option"><input type="radio" name="screen_background" value="dots" @checked($preferences['screen_background'] === 'dots')><span class="background-preview background-preview-dots"><b>Puntos</b></span></label>
        <label class="customization-option"><input type="radio" name="screen_background" value="gradient" @checked($preferences['screen_background'] === 'gradient')><span class="background-preview background-preview-paper"><b>Gradiente</b></span></label>
      </div>
    </fieldset>

    <fieldset>
      <legend>Colores</legend>
      <div class="form-inline">
        <label>
          Color de navbar
          <input class="color-bar-input" type="color" name="navbar_color" value="{{ $preferences['navbar_color'] }}">
        </label>
        <label>
          Color de letra principal
          <input class="color-bar-input" type="color" name="text_color" value="{{ $preferences['text_color'] }}">
        </label>
      </div>
    </fieldset>

    <button class="button" type="submit">Guardar personalización <span>→</span></button>
  </form>
</section>

<script>
  const userPresetGrid = document.getElementById('user-preset-grid');
  userPresetGrid?.querySelectorAll('.preset-card').forEach((card) => {
    card.addEventListener('click', () => {
      const { font, mode, bg, nav, text } = card.dataset;

      const fontInput = document.querySelector(`input[name="font_family"][value="${font}"]`);
      const modeInput = document.querySelector(`input[name="theme_mode"][value="${mode}"]`);
      const bgInput = document.querySelector(`input[name="screen_background"][value="${bg}"]`);
      const navInput = document.querySelector('input[name="navbar_color"]');
      const textInput = document.querySelector('input[name="text_color"]');

      if (fontInput) fontInput.checked = true;
      if (modeInput) modeInput.checked = true;
      if (bgInput) bgInput.checked = true;
      if (navInput && nav) navInput.value = nav;
      if (textInput && text) textInput.value = text;
    });
  });
</script>
@endsection
