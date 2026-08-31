@extends('layouts.app')

@section('content')
<section class="publication-editor wrap">
  <div class="editor-top"><div><p class="eyebrow">{{ $business->name }}</p><h1>Diseñá tu publicación.</h1><p class="muted">Mostrá tu producto, una oferta o una novedad antes de compartirla.</p></div><a class="button secondary" href="{{ route('posts.index', $business) }}">Cancelar</a></div>

  <form method="POST" enctype="multipart/form-data" action="{{ route('posts.store', $business) }}" class="publication-composer">
    @csrf
    <section class="editor-fields">
      <div class="editor-section"><span class="editor-step">01</span><div class="editor-section-copy"><p class="eyebrow">Mensaje principal</p><h2>Contá qué querés ofrecer.</h2></div></div>
      <div class="editor-details-grid">
        <label class="editor-wide-field">Título<input id="post-title" name="title" required maxlength="140" placeholder="Ej. Kit de cuidado facial"></label>
        <label class="editor-wide-field">Descripción<textarea id="post-body" name="body" rows="5" required placeholder="Contá por qué vale la pena y qué incluye."></textarea></label>
        <label class="editor-wide-field">Tipo<select name="type"><option value="product">Producto</option><option value="offer">Oferta</option><option value="news">Novedad</option></select></label>
        <label>Precio<input id="post-price" type="number" step="0.01" min="0" name="price" required placeholder="Ej. 2500"></label>
        <label>Precio oferta<input type="number" step="0.01" min="0" name="discount_price" placeholder="Opcional"></label>
        <label>Válida desde<input type="date" name="starts_at"></label>
        <label>Válida hasta<input type="date" name="ends_at"></label>
      </div>

      <div class="editor-section compact"><span class="editor-step">02</span><div class="editor-section-copy"><p class="eyebrow">Imágenes</p><h2>Elegí hasta cuatro fotos.</h2></div></div>
      <label class="photo-upload">Fotos del producto <span class="label-hint">JPG, PNG o WEBP · hasta 5 MB cada una</span><input id="post-photos" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple><span class="photo-upload-button">Elegir fotos</span></label>
      <div id="photo-previews" class="editor-photo-previews" aria-live="polite"></div>

      <div class="editor-section compact"><span class="editor-step">03</span><div class="editor-section-copy"><p class="eyebrow">Alcance</p><h2>Decidí dónde compartirla.</h2></div></div>
      <div class="publish-options"><label class="check"><input type="checkbox" name="is_published" value="1" checked><span>Publicar ahora en mi emprendimiento</span></label><label class="check"><input type="checkbox" name="share_on_social" value="1" @disabled($socialLinks->isEmpty())><span>Publicar también en redes sociales</span></label>@if($socialLinks->isEmpty())<small class="muted">Agregá una red desde Publicaciones para habilitar esta opción.</small>@endif</div>
      <button class="button editor-submit">Publicar <span>→</span></button>
    </section>

    <aside class="publication-preview"><div class="preview-label"><span>Vista previa</span><i></i></div><article class="offer-preview"><div id="preview-image" class="preview-image"><span>Tu foto</span></div><div class="preview-copy"><span class="tag">Nueva publicación</span><h2 id="preview-title">Tu título aparece acá</h2><p id="preview-body">La descripción ayudará a que tus clientes entiendan rápido tu propuesta.</p><strong id="preview-price">$ 0</strong><small>Publicado por {{ $business->name }}</small></div></article></aside>
  </form>
</section>
<script>
  const mirror = (source, target, fallback) => document.getElementById(source).addEventListener('input', (event) => document.getElementById(target).textContent = event.target.value || fallback);
  mirror('post-title', 'preview-title', 'Tu título aparece acá');
  mirror('post-body', 'preview-body', 'La descripción ayudará a que tus clientes entiendan rápido tu propuesta.');
  document.getElementById('post-price').addEventListener('input', (event) => document.getElementById('preview-price').textContent = `$ ${Number(event.target.value || 0).toLocaleString('es-AR')}`);
  document.getElementById('post-photos').addEventListener('change', (event) => {
    const previews = document.getElementById('photo-previews');
    const image = document.getElementById('preview-image');
    const files = Array.from(event.target.files).slice(0, 4);
    previews.replaceChildren();
    if (!files.length) return;
    files.forEach((file, index) => {
      const url = URL.createObjectURL(file);
      const thumbnail = document.createElement('img');
      thumbnail.src = url;
      thumbnail.alt = `Vista previa ${index + 1}`;
      previews.appendChild(thumbnail);
      if (index === 0) { image.style.backgroundImage = `url(${url})`; image.classList.add('has-image'); image.replaceChildren(); }
    });
  });
</script>
@endsection
