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
  <form method="POST" enctype="multipart/form-data" action="{{ route('business.customization.update', $business) }}" class="customization-form">
    @csrf
    <fieldset>
      <legend>Presets rápidos del perfil público</legend>
      <div class="preset-grid" id="business-preset-grid">
        <button type="button" class="preset-card" data-background="plain" data-font="dm" data-border="subtle" data-button="solid" data-card="standard" data-primary="#f6faf6" data-secondary="#176b61" data-text="#18352e" data-nav="#e3f3e9" data-post="#e3f3e9"><strong>Minimal</strong><small>Fresco y limpio para lectura rápida.</small></button>
        <button type="button" class="preset-card" data-background="paper" data-font="serif" data-border="rounded" data-button="rounded" data-card="soft" data-primary="#fffaf1" data-secondary="#9a4d12" data-text="#3b2517" data-nav="#fff0cf" data-post="#ffe8bf"><strong>Tierra</strong><small>Cálido y cercano para tiendas.</small></button>
        <button type="button" class="preset-card" data-background="grid" data-font="space" data-border="bold" data-button="outline" data-card="cut" data-primary="#f5fbfc" data-secondary="#176889" data-text="#17313d" data-nav="#d9edf4" data-post="#d8e8ee"><strong>Océano</strong><small>Profesional con acento azul.</small></button>
        <button type="button" class="preset-card" data-background="dots" data-font="manrope" data-border="rounded" data-button="soft" data-card="organic" data-primary="#fff8f7" data-secondary="#a5384f" data-text="#3d2027" data-nav="#ffe7e5" data-post="#f7d7d9"><strong>Coral</strong><small>Activo y enérgico para ofertas.</small></button>
      </div>
    </fieldset>

    <fieldset>
      <legend>Bordes de las cards</legend>
      <div class="customization-options border-options">
        <label class="customization-option"><input type="radio" name="public_border_type" value="subtle" @checked(($business->public_border_type ?? 'standard') === 'subtle')><span class="border-preview border-preview-subtle"><b>Sutil</b><small>Delicado y liviano</small></span></label>
        <label class="customization-option"><input type="radio" name="public_border_type" value="standard" @checked(($business->public_border_type ?? 'standard') === 'standard')><span class="border-preview border-preview-standard"><b>Estándar</b><small>Equilibrado</small></span></label>
        <label class="customization-option"><input type="radio" name="public_border_type" value="bold" @checked(($business->public_border_type ?? 'standard') === 'bold')><span class="border-preview border-preview-bold"><b>Marcado</b><small>Más presencia</small></span></label>
        <label class="customization-option"><input type="radio" name="public_border_type" value="rounded" @checked(($business->public_border_type ?? 'standard') === 'rounded')><span class="border-preview border-preview-rounded"><b>Redondeado</b><small>Suave y cercano</small></span></label>
      </div>
    </fieldset>

    <fieldset>
      <legend>Estilo de botones</legend>
      <div class="customization-options button-options">
        <label class="customization-option"><input type="radio" name="public_button_style" value="solid" @checked(($business->public_button_style ?? 'solid') === 'solid')><span class="button-preview button-preview-solid"><b>Sólido</b><small>Directo y fuerte</small></span></label>
        <label class="customization-option"><input type="radio" name="public_button_style" value="outline" @checked(($business->public_button_style ?? 'solid') === 'outline')><span class="button-preview button-preview-outline"><b>Contorno</b><small>Liviano y claro</small></span></label>
        <label class="customization-option"><input type="radio" name="public_button_style" value="ghost" @checked(($business->public_button_style ?? 'solid') === 'ghost')><span class="button-preview button-preview-ghost"><b>Fantasma</b><small>Minimalista</small></span></label>
        <label class="customization-option"><input type="radio" name="public_button_style" value="rounded" @checked(($business->public_button_style ?? 'solid') === 'rounded')><span class="button-preview button-preview-rounded"><b>Redondo</b><small>Amable y suave</small></span></label>
        <label class="customization-option"><input type="radio" name="public_button_style" value="soft" @checked(($business->public_button_style ?? 'solid') === 'soft')><span class="button-preview button-preview-soft"><b>Suave</b><small>Color tenue</small></span></label>
        <label class="customization-option"><input type="radio" name="public_button_style" value="sharp" @checked(($business->public_button_style ?? 'solid') === 'sharp')><span class="button-preview button-preview-sharp"><b>Recto</b><small>Preciso y firme</small></span></label>
        <label class="customization-option"><input type="radio" name="public_button_style" value="underline" @checked(($business->public_button_style ?? 'solid') === 'underline')><span class="button-preview button-preview-underline"><b>Subrayado</b><small>Editorial</small></span></label>
        <label class="customization-option"><input type="radio" name="public_button_style" value="block" @checked(($business->public_button_style ?? 'solid') === 'block')><span class="button-preview button-preview-block"><b>Bloque</b><small>Presencia total</small></span></label>
      </div>
    </fieldset>

    <fieldset>
      <legend>Forma de las cards</legend>
      <div class="customization-options card-shape-options">
        <label class="customization-option"><input type="radio" name="public_card_shape" value="standard" @checked(($business->public_card_shape ?? 'standard') === 'standard')><span class="card-shape-preview card-shape-standard"><b>Estándar</b><small>Equilibrada</small></span></label>
        <label class="customization-option"><input type="radio" name="public_card_shape" value="soft" @checked(($business->public_card_shape ?? 'standard') === 'soft')><span class="card-shape-preview card-shape-soft"><b>Suave</b><small>Esquinas leves</small></span></label>
        <label class="customization-option"><input type="radio" name="public_card_shape" value="rounded" @checked(($business->public_card_shape ?? 'standard') === 'rounded')><span class="card-shape-preview card-shape-rounded"><b>Redonda</b><small>Amable</small></span></label>
        <label class="customization-option"><input type="radio" name="public_card_shape" value="pill" @checked(($business->public_card_shape ?? 'standard') === 'pill')><span class="card-shape-preview card-shape-pill"><b>Píldora</b><small>Muy curva</small></span></label>
        <label class="customization-option"><input type="radio" name="public_card_shape" value="cut" @checked(($business->public_card_shape ?? 'standard') === 'cut')><span class="card-shape-preview card-shape-cut"><b>Cortada</b><small>Geométrica</small></span></label>
        <label class="customization-option"><input type="radio" name="public_card_shape" value="organic" @checked(($business->public_card_shape ?? 'standard') === 'organic')><span class="card-shape-preview card-shape-organic"><b>Orgánica</b><small>Irregular</small></span></label>
        <label class="customization-option"><input type="radio" name="public_card_shape" value="blob" @checked(($business->public_card_shape ?? 'standard') === 'blob')><span class="card-shape-preview card-shape-blob"><b>Blob</b><small>Orgánica y expresiva</small></span></label>
        <label class="customization-option"><input type="radio" name="public_card_shape" value="ticket" @checked(($business->public_card_shape ?? 'standard') === 'ticket')><span class="card-shape-preview card-shape-ticket"><b>Ticket</b><small>Recortada y exótica</small></span></label>
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
        <label class="customization-option"><input type="radio" name="public_background" value="custom" @checked(($business->public_background ?? 'plain') === 'custom')><span class="background-preview background-preview-custom"><b>Fondo propio</b></span></label>
      </div>
      <div class="custom-background-controls">
        <label>Imagen de fondo
          <input type="file" id="public-background-image" name="public_background_image" accept="image/jpeg,image/png,image/webp">
          <small>Elegí una imagen y luego decidí cómo mostrarla.</small>
        </label>
        <label>Modo de imagen
          <select id="public-background-image-mode" name="public_background_image_mode">
            <option value="full" @selected(($business->public_background_image_mode ?? 'full') === 'full')>Imagen completa</option>
            <option value="pattern" @selected(($business->public_background_image_mode ?? 'full') === 'pattern')>Convertir en patrón</option>
          </select>
        </label>
        <label>Tamaño del patrón
          <select id="public-background-pattern-size" name="public_background_pattern_size">
            <option value="80" @selected(($business->public_background_pattern_size ?? 180) == 80)>Pequeño</option>
            <option value="140" @selected(($business->public_background_pattern_size ?? 180) == 140)>Mediano</option>
            <option value="220" @selected(($business->public_background_pattern_size ?? 180) == 220)>Grande</option>
            <option value="320" @selected(($business->public_background_pattern_size ?? 180) == 320)>Extra grande</option>
          </select>
          <small>Solo se aplica al modo patrón.</small>
        </label>
      </div>
    </fieldset>

    <fieldset>
      <legend>Foto de perfil del emprendimiento</legend>
      <div class="profile-photo-customizer">
        <div class="profile-photo-editor">
          <div class="profile-photo-crop-frame profile-photo-style-{{ $business->profile_photo_style ?? 'circle' }}" id="profile-photo-crop-frame">
            @if($business->profile_photo_url)
              <img id="profile-photo-crop-image" src="{{ $business->profile_photo_url }}" alt="Vista previa de la foto de {{ $business->name }}" style="object-position: {{ $business->profile_photo_position_x ?? 50 }}% {{ $business->profile_photo_position_y ?? 50 }}%; transform: scale({{ (($business->profile_photo_zoom ?? 100) / 100) }});">
            @else
              <span id="profile-photo-crop-placeholder">{{ Str::substr($business->name, 0, 1) }}</span>
              <img id="profile-photo-crop-image" alt="Vista previa de la foto" hidden>
            @endif
          </div>
          <label class="photo-upload-button profile-photo-upload">Subir una foto de perfil
            <input id="profile-photo-input" type="file" name="profile_photo" accept="image/*">
          </label>
          <small class="muted">Acepta imágenes del dispositivo. La vista se adapta al formato elegido.</small>
        </div>
        <div class="profile-photo-controls">
          <strong>Diseño de la foto</strong>
          <div class="profile-photo-style-options">
            @foreach(['circle' => 'Círculo', 'square' => 'Cuadrada', 'rounded' => 'Redondeada', 'hexagon' => 'Hexágono', 'diamond' => 'Diamante', 'blob' => 'Orgánica'] as $style => $label)
              <label class="profile-photo-style-option"><input type="radio" name="profile_photo_style" value="{{ $style }}" @checked(($business->profile_photo_style ?? 'circle') === $style)><span class="profile-photo-style-sample profile-photo-style-{{ $style }}">Aa</span><small>{{ $label }}</small></label>
            @endforeach
          </div>
          <label class="crop-range"><span>Encuadre horizontal <output id="profile-photo-position-x-value">{{ $business->profile_photo_position_x ?? 50 }}%</output></span><input id="profile-photo-position-x" type="range" name="profile_photo_position_x" min="0" max="100" value="{{ $business->profile_photo_position_x ?? 50 }}" aria-label="Encuadre horizontal"></label>
          <label class="crop-range"><span>Encuadre vertical <output id="profile-photo-position-y-value">{{ $business->profile_photo_position_y ?? 50 }}%</output></span><input id="profile-photo-position-y" type="range" name="profile_photo_position_y" min="0" max="100" value="{{ $business->profile_photo_position_y ?? 50 }}" aria-label="Encuadre vertical"></label>
          <label class="crop-range"><span>Zoom <output id="profile-photo-zoom-value">{{ $business->profile_photo_zoom ?? 100 }}%</output></span><input id="profile-photo-zoom" type="range" name="profile_photo_zoom" min="100" max="220" value="{{ $business->profile_photo_zoom ?? 100 }}" aria-label="Zoom de la foto"></label>
        </div>
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
        <label>Color de botones<input class="color-bar-input" type="color" name="public_button_color" value="{{ $business->public_button_color ?? '#176b61' }}"><small>Acciones del perfil público.</small></label>
      </div>
      <p class="muted field-help">Los colores personalizados reemplazan los valores base del diseño.</p>
    </fieldset>

    <button class="button" type="submit">Guardar personalización <span>→</span></button>
  </form>

  <aside class="customization-preview-shell">
    <div class="preview-label"><span>Vista previa</span><i></i></div>
    <div class="customization-preview public-page public-background-{{ $business->public_background ?? 'plain' }} public-font-{{ $business->public_font_family ?? 'dm' }} public-border-{{ in_array($business->public_border_type, ['subtle', 'standard', 'bold', 'rounded']) ? $business->public_border_type : 'standard' }} public-button-{{ $business->public_button_style ?? 'solid' }} public-card-{{ $business->public_card_shape ?? 'standard' }}" id="business-preview" @if($business->public_background_image) style="--public-custom-image: url('{{ asset('storage/'.$business->public_background_image) }}');" @endif>
      <div class="preview-nav"><strong>{{ $business->name }}</strong><span>Explorar</span><span>Contacto</span></div>
      <div class="preview-hero"><div class="preview-logo profile-photo-style-{{ $business->profile_photo_style ?? 'circle' }}" id="business-preview-logo">@if($business->profile_photo_url)<img src="{{ $business->profile_photo_url }}" alt="">@else{{ Str::substr($business->name, 0, 1) }}@endif</div><div><small>{{ $business->category }}</small><h2>{{ $business->name }}</h2><p>{{ Str::limit($business->description, 70) }}</p></div></div>
      <div class="preview-content"><small>PUBLICACIONES</small><div class="preview-posts"><article><b>Novedades</b><span>Lo nuevo de este emprendimiento.</span></article><article><b>Productos</b><span>Propuestas para conocer.</span></article></div><button type="button" class="button preview-action">Ver emprendimiento <span>→</span></button></div>
      <div class="preview-footer">{{ $business->name }} <span>Un espacio propio para crecer.</span></div>
    </div>
  </aside>
  </div>
</section>
<script>
  const businessPresetGrid = document.getElementById('business-preset-grid');
  businessPresetGrid?.querySelectorAll('.preset-card').forEach((card) => {
    card.addEventListener('click', () => {
      const { background, font, border, button, card, primary, secondary, text, nav, post } = card.dataset;
      const backgroundInput = document.querySelector(`input[name="public_background"][value="${background}"]`);
      const fontInput = document.querySelector(`input[name="public_font_family"][value="${font}"]`);
      const navInput = document.querySelector('input[name="public_navbar_color"]');
      const postInput = document.querySelector('input[name="public_posts_background"]');
      const primaryInput = document.querySelector('input[name="public_primary_color"]');
      const secondaryInput = document.querySelector('input[name="public_secondary_color"]');
      const textInput = document.querySelector('input[name="public_text_color"]');
      const borderInput = document.querySelector(`input[name="public_border_type"][value="${border}"]`);
      const buttonInput = document.querySelector(`input[name="public_button_style"][value="${button}"]`);
      const cardInput = document.querySelector(`input[name="public_card_shape"][value="${card}"]`);
      if (backgroundInput) backgroundInput.checked = true;
      if (fontInput) fontInput.checked = true;
      if (primaryInput && primary) primaryInput.value = primary;
      if (secondaryInput && secondary) secondaryInput.value = secondary;
      if (textInput && text) textInput.value = text;
      if (borderInput) borderInput.checked = true;
      if (buttonInput) buttonInput.checked = true;
      if (cardInput) cardInput.checked = true;
      if (navInput && nav) navInput.value = nav;
      if (postInput && post) postInput.value = post;
      applyBusinessPreview();
    });
  });

  const businessPreview = document.getElementById('business-preview');
  const profilePhotoCropFrame = document.getElementById('profile-photo-crop-frame');
  const profilePhotoCropImage = document.getElementById('profile-photo-crop-image');
  const profilePhotoPlaceholder = document.getElementById('profile-photo-crop-placeholder');
  const businessPreviewLogo = document.getElementById('business-preview-logo');
  const profilePhotoInput = document.getElementById('profile-photo-input');
  const profilePhotoPositionX = document.getElementById('profile-photo-position-x');
  const profilePhotoPositionY = document.getElementById('profile-photo-position-y');
  const profilePhotoZoom = document.getElementById('profile-photo-zoom');
  const profilePhotoPositionXValue = document.getElementById('profile-photo-position-x-value');
  const profilePhotoPositionYValue = document.getElementById('profile-photo-position-y-value');
  const profilePhotoZoomValue = document.getElementById('profile-photo-zoom-value');

  function setProfilePhotoTransform(image, positionX, positionY, zoom) {
    if (!image) return;
    const scale = Number(zoom);
    const translateX = ((50 - Number(positionX)) / 50 * (scale - 1) / 2 * 100).toFixed(2);
    const translateY = ((50 - Number(positionY)) / 50 * (scale - 1) / 2 * 100).toFixed(2);
    image.style.setProperty('--photo-translate-x', `${translateX}%`);
    image.style.setProperty('--photo-translate-y', `${translateY}%`);
    image.style.transform = `translate(${translateX}%, ${translateY}%) scale(${scale})`;
  }

  function applyProfilePhotoPreview() {
    const style = document.querySelector('input[name="profile_photo_style"]:checked')?.value || 'circle';
    const positionX = profilePhotoPositionX?.value || '50';
    const positionY = profilePhotoPositionY?.value || '50';
    const zoom = (Number(profilePhotoZoom?.value || 100) / 100 * 1.2).toFixed(2);
    if (profilePhotoPositionXValue) profilePhotoPositionXValue.textContent = `${positionX}%`;
    if (profilePhotoPositionYValue) profilePhotoPositionYValue.textContent = `${positionY}%`;
    if (profilePhotoZoomValue) profilePhotoZoomValue.textContent = `${profilePhotoZoom?.value || 100}%`;
    [profilePhotoCropFrame, businessPreviewLogo].forEach((element) => {
      if (!element) return;
      element.className = element.className.replace(/profile-photo-style-\S+/g, '').trim();
      element.classList.add(`profile-photo-style-${style}`);
    });
    if (profilePhotoCropImage) {
      profilePhotoCropImage.style.setProperty('--photo-position-x', `${positionX}%`);
      profilePhotoCropImage.style.setProperty('--photo-position-y', `${positionY}%`);
      profilePhotoCropImage.style.objectPosition = `${positionX}% ${positionY}%`;
      setProfilePhotoTransform(profilePhotoCropImage, positionX, positionY, zoom);
    }
    if (profilePhotoCropFrame) {
      profilePhotoCropFrame.style.setProperty('--photo-position-x', `${positionX}%`);
      profilePhotoCropFrame.style.setProperty('--photo-position-y', `${positionY}%`);
      profilePhotoCropFrame.style.setProperty('--photo-zoom', zoom);
    }
    if (businessPreviewLogo) {
      businessPreviewLogo.style.setProperty('--photo-position-x', `${positionX}%`);
      businessPreviewLogo.style.setProperty('--photo-position-y', `${positionY}%`);
      businessPreviewLogo.style.setProperty('--photo-zoom', zoom);
      const previewImage = businessPreviewLogo.querySelector('img');
      if (previewImage) {
        previewImage.style.objectPosition = `${positionX}% ${positionY}%`;
        setProfilePhotoTransform(previewImage, positionX, positionY, zoom);
      }
    }
  }

  function applyBusinessPreview() {
    if (!businessPreview) return;
    const font = document.querySelector('input[name="public_font_family"]:checked')?.value || 'dm';
    const background = document.querySelector('input[name="public_background"]:checked')?.value || 'plain';
    const border = document.querySelector('input[name="public_border_type"]:checked')?.value || 'standard';
    const button = document.querySelector('input[name="public_button_style"]:checked')?.value || 'solid';
    const card = document.querySelector('input[name="public_card_shape"]:checked')?.value || 'standard';
    businessPreview.className = businessPreview.className.replace(/public-font-\S+|public-background-\S+|public-border-\S+|public-button-\S+|public-card-\S+/g, '').trim();
    businessPreview.classList.add(`public-font-${font}`, `public-background-${background}`, `public-border-${border}`, `public-button-${button}`, `public-card-${card}`);
    businessPreview.style.setProperty('--public-primary', document.querySelector('input[name="public_primary_color"]')?.value || '#f6faf6');
    businessPreview.style.setProperty('--public-secondary', document.querySelector('input[name="public_secondary_color"]')?.value || '#176b61');
    businessPreview.style.setProperty('--public-ink', document.querySelector('input[name="public_text_color"]')?.value || '#18352e');
    businessPreview.style.setProperty('--public-button-color', document.querySelector('input[name="public_button_color"]')?.value || '#176b61');
    businessPreview.style.setProperty('--public-nav-preview', document.querySelector('input[name="public_navbar_color"]')?.value || '#eef1ea');
    businessPreview.style.setProperty('--public-post-bg', document.querySelector('input[name="public_posts_background"]')?.value || '#e8e6b6');
    applyProfilePhotoPreview();
    const imageInput = document.querySelector('#public-background-image');
    const imageMode = document.querySelector('#public-background-image-mode')?.value || 'full';
    const patternSize = document.querySelector('#public-background-pattern-size')?.value || '180';
    businessPreview.classList.toggle('public-background-custom', background === 'custom');
    businessPreview.classList.toggle('public-background-custom-pattern', background === 'custom' && imageMode === 'pattern');
    businessPreview.style.setProperty('--public-pattern-size', `${patternSize}px`);
    if (imageInput?.files?.[0]) businessPreview.style.setProperty('--public-custom-image', `url('${URL.createObjectURL(imageInput.files[0])}')`);
  }

  document.querySelectorAll('.customization-form input').forEach((input) => {
    input.addEventListener('input', applyBusinessPreview);
    input.addEventListener('change', applyBusinessPreview);
  });
  document.querySelectorAll('#public-background-image-mode, #public-background-pattern-size').forEach((input) => input.addEventListener('change', applyBusinessPreview));
  document.querySelectorAll('input[name="profile_photo_style"], #profile-photo-position-x, #profile-photo-position-y, #profile-photo-zoom').forEach((input) => input.addEventListener('input', applyProfilePhotoPreview));
  profilePhotoInput?.addEventListener('change', () => {
    const file = profilePhotoInput.files?.[0];
    if (!file || !profilePhotoCropImage) return;
    const imageUrl = URL.createObjectURL(file);
    profilePhotoCropImage.src = imageUrl;
    profilePhotoCropImage.hidden = false;
    if (profilePhotoPlaceholder) profilePhotoPlaceholder.hidden = true;
    if (businessPreviewLogo) {
      businessPreviewLogo.innerHTML = '<img alt="">';
      businessPreviewLogo.querySelector('img').src = imageUrl;
    }
    applyProfilePhotoPreview();
  });
  document.querySelector('#public-background-image')?.addEventListener('change', () => {
    const customBackground = document.querySelector('input[name="public_background"][value="custom"]');
    if (customBackground) customBackground.checked = true;
    applyBusinessPreview();
  });
  applyBusinessPreview();
</script>
@endsection
