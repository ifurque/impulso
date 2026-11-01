@extends('layouts.app')

{{-- Vista: editor para crear una publicación (oferta, producto o novedad) con vista previa en vivo --}}
@section('content')
  <section class="publication-editor wrap">
    {{-- Encabezado con el nombre del negocio y botón para cancelar --}}
    <div class="editor-top">
      <div>
        <p class="eyebrow">{{ $business->name }}</p>
        <h1>Diseñá tu publicación.</h1>
        <p class="muted">Mostrá tu producto, una oferta o una novedad antes de compartirla.</p>
      </div><a class="button secondary" href="{{ route('posts.index', $business) }}">Cancelar</a>
    </div>

    {{-- Formulario de la publicación (multipart por las fotos) --}}
    <form method="POST" enctype="multipart/form-data" action="{{ route('posts.store', $business) }}"
      class="publication-composer">
      @csrf
      {{-- Columna de campos, organizada en 4 pasos --}}
      <section class="editor-fields">
        {{-- Paso 01: título, descripción, precios y vigencia --}}
        <div class="editor-section"><span class="editor-step">01</span>
          <div class="editor-section-copy">
            <p class="eyebrow">Mensaje principal</p>
            <h2>Contá qué querés ofrecer.</h2>
          </div>
        </div>
        <div class="editor-details-grid">
          <label class="editor-wide-field">Título<input id="post-title" name="title" required maxlength="140"
              placeholder="Ej. Kit de cuidado facial"></label>
          <label class="editor-wide-field">Descripción<textarea id="post-body" name="body" rows="5" required
              placeholder="Contá por qué vale la pena y qué incluye."></textarea></label>
          <label>Precio<input id="post-price" type="number" step="0.01" min="0" name="price" required
              placeholder="Ej. 2500"></label>
          <label>Precio oferta<input type="number" step="0.01" min="0" name="discount_price"
              placeholder="Opcional"></label>
          <label>Válida desde<input type="date" name="starts_at"></label>
          <label>Válida hasta<input type="date" name="ends_at"></label>
        </div>

        {{-- Paso 02: selección de productos del stock y cantidades --}}
        <div class="editor-section compact"><span class="editor-step">02</span>
          <div class="editor-section-copy">
            <p class="eyebrow">Productos del stock</p>
            <h2>Elegí productos ya creados y sus cantidades.</h2>
          </div>
        </div>
        @if($products->isEmpty())
          <p class="muted">Primero crea productos en la base de datos del emprendimiento para poder armarlas en una
            publicación.</p>
        @else
          <div class="stock-publication-products">
            @foreach($products as $product)
              <div class="stock-publication-product-row">
                <label class="check"><input type="checkbox" name="selected_products[{{ $loop->index }}][product_id]"
                    value="{{ $product->id }}"> <span>{{ $product->name }} (Stock: {{ $product->quantity }})</span></label>
                <label class="qty-field">Cantidad<input type="number" min="1" max="999"
                    name="selected_products[{{ $loop->index }}][quantity]" value="1" placeholder="1"></label>
              </div>
            @endforeach
          </div>
        @endif

        {{-- Paso 03: subida de hasta cuatro fotos con miniaturas --}}
        <div class="editor-section compact"><span class="editor-step">03</span>
          <div class="editor-section-copy">
            <p class="eyebrow">Imágenes</p>
            <h2>Elegí hasta cuatro fotos.</h2>
          </div>
        </div>
        <label class="photo-upload">Fotos del producto <span class="label-hint">JPG, PNG o WEBP · hasta 5 MB cada
            una</span><input id="post-photos" type="file" name="photos[]" accept="image/jpeg,image/png,image/webp"
            multiple><span class="photo-upload-button">Elegir fotos</span></label>
        <div id="photo-previews" class="editor-photo-previews" aria-live="polite"></div>

        {{-- Paso 04: alcance (publicar ahora y/o en redes sociales) y botón de envío --}}
        <div class="editor-section compact"><span class="editor-step">04</span>
          <div class="editor-section-copy">
            <p class="eyebrow">Alcance</p>
            <h2>Decidí dónde compartirla.</h2>
          </div>
        </div>
        <div class="publish-options"><label class="check"><input type="checkbox" name="is_published" value="1"
              checked><span>Publicar ahora en mi emprendimiento</span></label><label class="check"><input type="checkbox"
              name="share_on_social" value="1" @disabled($socialLinks->isEmpty())><span>Publicar también en redes
              sociales</span></label>@if($socialLinks->isEmpty())<small class="muted">Agregá una red desde Publicaciones
              para habilitar esta opción.</small>@endif</div>
        <button class="button editor-submit">Publicar <span>→</span></button>
      </section>

      {{-- Vista previa en vivo de la publicación (actualizada por el script de abajo) --}}
      <aside class="publication-preview">
        <div class="preview-label"><span>Vista previa</span><i></i></div>
        <article class="offer-preview">
          <div id="preview-image" class="preview-image"><span>Tu foto</span></div>
          <div class="preview-copy"><span class="tag">Nueva publicación</span>
            <h2 id="preview-title">Tu título aparece acá</h2>
            <p id="preview-body">La descripción ayudará a que tus clientes entiendan rápido tu propuesta.</p><strong
              id="preview-price">$ 0</strong><small>Publicado por {{ $business->name }}</small>
          </div>
        </article>
      </aside>
    </form>
  </section>
  <script>
    // Refleja título, descripción y precio en la vista previa mientras se escribe
    const mirror = (source, target, fallback) => document.getElementById(source).addEventListener('input', (event) => document.getElementById(target).textContent = event.target.value || fallback);
    mirror('post-title', 'preview-title', 'Tu título aparece acá');
    mirror('post-body', 'preview-body', 'La descripción ayudará a que tus clientes entiendan rápido tu propuesta.');
    document.getElementById('post-price').addEventListener('input', (event) => document.getElementById('preview-price').textContent = `$ ${Number(event.target.value || 0).toLocaleString('es-AR')}`);
    // Muestra miniaturas de hasta 4 fotos y usa la primera como imagen de la vista previa
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