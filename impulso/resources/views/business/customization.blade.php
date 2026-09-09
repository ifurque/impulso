@extends('layouts.app')
@section('content')
<section class="form-page wrap">
  <div class="dashboard-top">
    <div>
      <p class="eyebrow">Perfil público de {{ $business->name }}</p>
      <h1>Personalizá tu página.</h1>
      <p class="muted">Elegí fuente, fondo y colores para que tu perfil se sienta propio y fácil de leer.</p>
    </div>
    <a class="button secondary" href="{{ route('business.show', $business) }}">Ver perfil <span>↗</span></a>
  </div>

  <div class="customization-workspace">
  <form method="POST" action="{{ route('business.customization.update', $business) }}" class="customization-form">
    @csrf
    <fieldset>
      <legend>Presets rápidos del perfil público</legend>
      <div class="preset-grid" id="business-preset-grid">
        <button type="button" class="preset-card" data-background="plain" data-font="dm" data-primary="#f6faf6" data-secondary="#176b61" data-text="#18352e" data-nav="#e3f3e9" data-post="#e3f3e9"><strong>Minimal</strong><small>Fresco y limpio para lectura rápida.</small></button>
        <button type="button" class="preset-card" data-background="paper" data-font="serif" data-primary="#fffaf1" data-secondary="#9a4d12" data-text="#3b2517" data-nav="#fff0cf" data-post="#ffe8bf"><strong>Tierra</strong><small>Cálido y cercano para tiendas.</small></button>
        <button type="button" class="preset-card" data-background="grid" data-font="space" data-primary="#f5fbfc" data-secondary="#176889" data-text="#17313d" data-nav="#d9edf4" data-post="#d8e8ee"><strong>Océano</strong><small>Profesional con acento azul.</small></button>
        <button type="button" class="preset-card" data-background="dots" data-font="manrope" data-primary="#fff8f7" data-secondary="#a5384f" data-text="#3d2027" data-nav="#ffe7e5" data-post="#f7d7d9"><strong>Coral</strong><small>Activo y enérgico para ofertas.</small></button>
      </div>
    </fieldset>

    <fieldset>
      <legend>Tipografía</legend>
      <div class="customization-options font-options">
        <label class="customization-option"><input type="radio" name="public_font_family" value="dm" @checked(($business->public_font_family ?? 'dm') === 'dm')><span class="font-preview font-preview-dm"><b>DM Sans</b><small>Clara y moderna</small></span></label>
        <label class="customization-option"><input type="radio" name="public_font_family" value="space" @checked(($business->public_font_family ?? 'dm') === 'space')><span class="font-preview font-preview-space"><b>Space Grotesk</b><small>Geométrica y expresiva</small></span></label>
        <label class="customization-option"><input type="radio" name="public_font_family" value="serif" @checked(($business->public_font_family ?? 'dm') === 'serif')><span class="font-preview font-preview-serif"><b>Merriweather</b><small>Editorial y cálida</small></span></label>
        <label class="customization-option"><input type="radio" name="public_font_family" value="mono" @checked(($business->public_font_family ?? 'dm') === 'mono')><span class="font-preview font-preview-mono"><b>JetBrains Mono</b><small>Precisa y técnica</small></span></label>
        <label class="customization-option"><input type="radio" name="public_font_family" value="fraunces" @checked(($business->public_font_family ?? 'dm') === 'fraunces')><span class="font-preview font-preview-fraunces"><b>Fraunces</b><small>Orgánica y editorial</small></span></label>
        <label class="customization-option"><input type="radio" name="public_font_family" value="manrope" @checked(($business->public_font_family ?? 'dm') === 'manrope')><span class="font-preview font-preview-manrope"><b>Manrope</b><small>Amable y contemporánea</small></span></label>
        <label class="customization-option"><input type="radio" name="public_font_family" value="plex" @checked(($business->public_font_family ?? 'dm') === 'plex')><span class="font-preview font-preview-plex"><b>IBM Plex Sans</b><small>Institucional y clara</small></span></label>
        <label class="customization-option"><input type="radio" name="public_font_family" value="fira" @checked(($business->public_font_family ?? 'dm') === 'fira')><span class="font-preview font-preview-fira"><b>Fira Code</b><small>Digital y monoespaciada</small></span></label>
      </div>
    </fieldset>

    <fieldset>
      <legend>Fondo</legend>
      <div class="customization-options background-options">
        @foreach($backgrounds as $key => $label)
          <label class="customization-option"><input type="radio" name="public_background" value="{{ $key }}" @checked(($business->public_background ?? 'plain') === $key)><span class="background-preview background-preview-{{ $key }}"><b>{{ $label }}</b></span></label>
        @endforeach
      </div>
    </fieldset>

    <fieldset>
      <legend>Colores</legend>
      <div class="form-inline customization-color-grid">
        <label>Fondo principal<input class="color-bar-input" type="color" name="public_primary_color" value="{{ $business->public_primary_color ?? '#f6faf6' }}"><small>Color base de tu perfil.</small></label>
        <label>Diseño / patrón<input class="color-bar-input" type="color" name="public_secondary_color" value="{{ $business->public_secondary_color ?? '#176b61' }}"><small>Grillas, puntos y detalles.</small></label>
        <label>Barra de navegación<input class="color-bar-input" type="color" name="public_navbar_color" value="{{ $business->public_navbar_color ?? '#eef1ea' }}"><small>Navbar y footer.</small></label>
        <label>Fondo de publicaciones y propuestas<input class="color-bar-input" type="color" name="public_posts_background" value="{{ $business->public_posts_background ?? '#e8e6b6' }}"><small>Novedades, productos y servicios.</small></label>
        <label>Color de texto<input class="color-bar-input" type="color" name="public_text_color" value="{{ $business->public_text_color ?? '#18352e' }}"><small>Títulos y textos principales.</small></label>
      </div>
      <p class="muted field-help">Los colores personalizados reemplazan los valores base del diseño.</p>
    </fieldset>

    <button class="button" type="submit">Guardar personalización <span>→</span></button>
  </form>

  <aside class="customization-preview-shell">
    <div class="preview-label"><span>Vista previa</span><i></i></div>
    <div class="customization-preview public-page public-background-{{ $business->public_background ?? 'plain' }} public-font-{{ $business->public_font_family ?? 'dm' }}" id="business-preview">
      <div class="preview-nav"><strong>{{ $business->name }}</strong><span>Explorar</span><span>Contacto</span></div>
      <div class="preview-hero"><div class="preview-logo">{{ Str::substr($business->name, 0, 1) }}</div><div><small>{{ $business->category }}</small><h2>{{ $business->name }}</h2><p>{{ Str::limit($business->description, 70) }}</p></div></div>
      <div class="preview-content"><small>PUBLICACIONES</small><div class="preview-posts"><article><b>Novedades</b><span>Lo nuevo de este emprendimiento.</span></article><article><b>Productos</b><span>Propuestas para conocer.</span></article></div></div>
      <div class="preview-footer">{{ $business->name }} <span>Un espacio propio para crecer.</span></div>
    </div>
  </aside>
  </div>
</section>
<script>
  const businessPresetGrid = document.getElementById('business-preset-grid');
  businessPresetGrid?.querySelectorAll('.preset-card').forEach((card) => {
    card.addEventListener('click', () => {
      const { background, font, primary, secondary, text, nav, post } = card.dataset;
      const backgroundInput = document.querySelector(`input[name="public_background"][value="${background}"]`);
      const fontInput = document.querySelector(`input[name="public_font_family"][value="${font}"]`);
      const navInput = document.querySelector('input[name="public_navbar_color"]');
      const postInput = document.querySelector('input[name="public_posts_background"]');
      const primaryInput = document.querySelector('input[name="public_primary_color"]');
      const secondaryInput = document.querySelector('input[name="public_secondary_color"]');
      const textInput = document.querySelector('input[name="public_text_color"]');
      if (backgroundInput) backgroundInput.checked = true;
      if (fontInput) fontInput.checked = true;
      if (primaryInput && primary) primaryInput.value = primary;
      if (secondaryInput && secondary) secondaryInput.value = secondary;
      if (textInput && text) textInput.value = text;
      if (navInput && nav) navInput.value = nav;
      if (postInput && post) postInput.value = post;
      applyBusinessPreview();
    });
  });

  const businessPreview = document.getElementById('business-preview');
  function applyBusinessPreview() {
    if (!businessPreview) return;
    const font = document.querySelector('input[name="public_font_family"]:checked')?.value || 'dm';
    const background = document.querySelector('input[name="public_background"]:checked')?.value || 'plain';
    businessPreview.className = businessPreview.className.replace(/public-font-\S+|public-background-\S+/g, '').trim();
    businessPreview.classList.add(`public-font-${font}`, `public-background-${background}`);
    businessPreview.style.setProperty('--public-primary', document.querySelector('input[name="public_primary_color"]')?.value || '#f6faf6');
    businessPreview.style.setProperty('--public-secondary', document.querySelector('input[name="public_secondary_color"]')?.value || '#176b61');
    businessPreview.style.setProperty('--public-ink', document.querySelector('input[name="public_text_color"]')?.value || '#18352e');
    businessPreview.style.setProperty('--public-nav-preview', document.querySelector('input[name="public_navbar_color"]')?.value || '#eef1ea');
    businessPreview.style.setProperty('--public-post-bg', document.querySelector('input[name="public_posts_background"]')?.value || '#e8e6b6');
  }

  document.querySelectorAll('input[name="public_font_family"], input[name="public_background"]').forEach((input) => input.addEventListener('change', applyBusinessPreview));
  document.querySelectorAll('input[type="color"]').forEach((input) => {
    input.addEventListener('input', applyBusinessPreview);
    input.addEventListener('change', applyBusinessPreview);
  });
  applyBusinessPreview();
</script>
@endsection
