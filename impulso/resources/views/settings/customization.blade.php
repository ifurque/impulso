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
        <button type="button" class="preset-card" data-font="dm" data-mode="day" data-bg="paper" data-primary="#f7f8f3" data-secondary="#dfe5dc" data-nav="#eef1ea" data-text="#182522">
          <strong>Minimal</strong>
          <small>Limpio, claro y equilibrado.</small>
        </button>
        <button type="button" class="preset-card" data-font="serif" data-mode="day" data-bg="gradient" data-primary="#f3e9d2" data-secondary="#d69b5d" data-nav="#f3e9d2" data-text="#2c241a">
          <strong>Tierra</strong>
          <small>Cálido, editorial y elegante.</small>
        </button>
        <button type="button" class="preset-card" data-font="space" data-mode="night" data-bg="paper" data-primary="#101514" data-secondary="#d7ee59" data-nav="#17201d" data-text="#f2faf5">
          <strong>Modo noche</strong>
          <small>Oscuro, claro y con acentos vivos.</small>
        </button>
        <button type="button" class="preset-card" data-font="mono" data-mode="day" data-bg="dots" data-primary="#dbe8e0" data-secondary="#10322b" data-nav="#dbe8e0" data-text="#10322b">
          <strong>Técnico</strong>
          <small>Preciso y legible para gestión diaria.</small>
        </button>
      </div>
    </fieldset>

    <fieldset>
      <legend>Tipografía</legend>
      <div class="customization-options font-options">
        <label class="customization-option"><input type="radio" name="font_family" value="dm" @checked($preferences['font_family'] === 'dm')><span class="font-preview font-preview-dm"><b>DM Sans</b><small>Clara y moderna</small></span></label>
        <label class="customization-option"><input type="radio" name="font_family" value="space" @checked($preferences['font_family'] === 'space')><span class="font-preview font-preview-space"><b>Space Grotesk</b><small>Geométrica y expresiva</small></span></label>
        <label class="customization-option"><input type="radio" name="font_family" value="serif" @checked($preferences['font_family'] === 'serif')><span class="font-preview font-preview-serif"><b>Merriweather</b><small>Editorial y cálida</small></span></label>
        <label class="customization-option"><input type="radio" name="font_family" value="mono" @checked($preferences['font_family'] === 'mono')><span class="font-preview font-preview-mono"><b>JetBrains Mono</b><small>Precisa y técnica</small></span></label>
        <label class="customization-option"><input type="radio" name="font_family" value="fraunces" @checked($preferences['font_family'] === 'fraunces')><span class="font-preview font-preview-fraunces"><b>Fraunces</b><small>Orgánica y editorial</small></span></label>
        <label class="customization-option"><input type="radio" name="font_family" value="manrope" @checked($preferences['font_family'] === 'manrope')><span class="font-preview font-preview-manrope"><b>Manrope</b><small>Amable y contemporánea</small></span></label>
        <label class="customization-option"><input type="radio" name="font_family" value="plex" @checked($preferences['font_family'] === 'plex')><span class="font-preview font-preview-plex"><b>IBM Plex Sans</b><small>Institucional y clara</small></span></label>
        <label class="customization-option"><input type="radio" name="font_family" value="fira" @checked($preferences['font_family'] === 'fira')><span class="font-preview font-preview-fira"><b>Fira Code</b><small>Digital y monoespaciada</small></span></label>
      </div>
    </fieldset>

    <input id="theme-mode-value" type="hidden" name="theme_mode" value="{{ $preferences['theme_mode'] }}">

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
          Fondo principal
          <input class="color-bar-input" type="color" name="primary_color" value="{{ $preferences['primary_color'] }}">
          <small>Color base de la página.</small>
        </label>
        <label>
          Diseño / patrón
          <input class="color-bar-input" type="color" name="secondary_color" value="{{ $preferences['secondary_color'] }}">
          <small>Grillas, puntos y detalles.</small>
        </label>
        <label>
          Barra de navegación
          <input class="color-bar-input" type="color" name="navbar_color" value="{{ $preferences['navbar_color'] }}">
          <small>Se mantiene separada del fondo.</small>
        </label>
        <label>
          Texto principal
          <input class="color-bar-input" type="color" name="text_color" value="{{ $preferences['text_color'] }}">
          <small>Color de lectura general.</small>
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
      const { font, mode, bg, primary, secondary, nav, text } = card.dataset;

      const fontInput = document.querySelector(`input[name="font_family"][value="${font}"]`);
      const modeInput = document.querySelector('#theme-mode-value');
      const bgInput = document.querySelector(`input[name="screen_background"][value="${bg}"]`);
      const primaryInput = document.querySelector('input[name="primary_color"]');
      const secondaryInput = document.querySelector('input[name="secondary_color"]');
      const navInput = document.querySelector('input[name="navbar_color"]');
      const textInput = document.querySelector('input[name="text_color"]');

      if (fontInput) fontInput.checked = true;
      if (modeInput) modeInput.value = mode;
      if (bgInput) bgInput.checked = true;
      if (primaryInput && primary) primaryInput.value = primary;
      if (secondaryInput && secondary) secondaryInput.value = secondary;
      if (navInput && nav) navInput.value = nav;
      if (textInput && text) textInput.value = text;
      applyUserPreview();
    });
  });

  const userBody = document.body;
  const userPreviewFields = {
    font: document.querySelector('input[name="font_family"]:checked'),
    mode: document.querySelector('#theme-mode-value'),
    background: document.querySelector('input[name="screen_background"]:checked'),
    primary: document.querySelector('input[name="primary_color"]'),
    secondary: document.querySelector('input[name="secondary_color"]'),
    navbar: document.querySelector('input[name="navbar_color"]'),
    text: document.querySelector('input[name="text_color"]'),
  };

  function applyUserPreview() {
    const selectedFont = document.querySelector('input[name="font_family"]:checked')?.value || 'dm';
    const selectedMode = document.querySelector('#theme-mode-value')?.value || 'day';
    const selectedBackground = document.querySelector('input[name="screen_background"]:checked')?.value || 'paper';
    userBody.className = userBody.className
      .replace(/user-font-\S+/g, '')
      .replace(/user-mode-\S+/g, '')
      .replace(/user-bg-\S+/g, '')
      .trim();
    userBody.classList.add(`user-font-${selectedFont}`, `user-mode-${selectedMode}`, `user-bg-${selectedBackground}`);
    userBody.style.setProperty('--user-primary', document.querySelector('input[name="primary_color"]')?.value || '#f7f8f3');
    userBody.style.setProperty('--user-secondary', document.querySelector('input[name="secondary_color"]')?.value || '#dfe5dc');
    userBody.style.setProperty('--user-navbar-bg', document.querySelector('input[name="navbar_color"]')?.value || '#eef1ea');
    userBody.style.setProperty('--user-text-color', document.querySelector('input[name="text_color"]')?.value || '#182522');
  }

  document.querySelectorAll('input[name="font_family"], input[name="screen_background"], input[type="color"]').forEach((input) => {
    input.addEventListener('change', applyUserPreview);
  });
  applyUserPreview();

</script>
@endsection
