@extends('layouts.app')

@section('content')
<section class="dashboard wrap">
  <div class="dashboard-top"><div><p class="eyebrow">Contenido del emprendimiento</p><h1>Publicaciones.</h1><p class="muted">Comparte productos, novedades y ofertas con tu comunidad.</p></div><div class="page-actions"><a class="button secondary" href="{{ route('dashboard', $business) }}">Volver al panel</a><a class="button" href="{{ route('posts.create', $business) }}">Crear publicación <span>+</span></a></div></div>

  <div class="dashboard-grid">
    <div class="panel">
      <p class="eyebrow">Redes sociales</p><h2>Conexiones</h2>
      <p class="muted">Al publicar en redes se priorizarán fotos, título y descripción.</p>
      <form method="POST" action="{{ route('social.store', $business) }}" class="form">@csrf
        <label>Red<select name="platform"><option value="instagram">Instagram</option><option value="facebook">Facebook</option><option value="whatsapp">WhatsApp</option><option value="tiktok">TikTok</option></select></label>
        <label>Enlace<input name="url" type="url" required placeholder="https://..."></label>
        <button class="button secondary">Guardar red</button>
      </form>
      @if($socialLinks->isNotEmpty())
        <div class="social-link-list">@foreach($socialLinks as $socialLink)<a href="{{ $socialLink->url }}" target="_blank" rel="noopener">{{ ucfirst($socialLink->platform) }} <span>↗</span></a>@endforeach</div>
      @endif
    </div>
  </div>

  <div class="panel recent">
    <div class="panel-head"><div><p class="eyebrow">Tu contenido</p><h2>Publicaciones creadas</h2></div></div>
    <div class="post-list">
      @forelse($posts as $post)
        <article class="post-row">
          <div>
            @php($photos = $post->photos ?: ($post->photo ? [$post->photo] : []))
            @if(count($photos))<div class="post-gallery">@foreach($photos as $photo)<img class="post-photo" src="{{ asset('storage/'.$photo) }}" alt="Foto de {{ $post->title }}">@endforeach</div>@endif
            <span class="tag">{{ $post->type === 'offer' ? 'Oferta' : ($post->type === 'product' ? 'Producto' : 'Novedad') }}</span>
            <h3>{{ $post->title }}</h3><p>{{ Str::limit($post->body, 150) }}</p>
            <strong>$ {{ number_format($post->price, 0, ',', '.') }}</strong>
            @if($post->discountPercentage())<strong class="discount-badge">{{ $post->discountPercentage() }}% OFF</strong>@endif
            <small>{{ $post->is_published ? 'Publicada' : 'Borrador' }} @if($post->share_on_social) · Redes sociales @endif @if($post->ends_at) · hasta {{ $post->ends_at->format('d/m/Y') }} @endif</small>
            <details><summary>Editar publicación</summary><form method="POST" enctype="multipart/form-data" action="{{ route('posts.update', [$business, $post]) }}" class="form">@csrf @method('PUT')<label>Título<input name="title" value="{{ $post->title }}" required></label><label>Precio<input type="number" step="0.01" min="0" name="price" value="{{ $post->price }}" required></label><label>Tipo<select name="type"><option value="product" @selected($post->type === 'product')>Producto</option><option value="offer" @selected($post->type === 'offer')>Oferta</option><option value="news" @selected($post->type === 'news')>Novedad</option></select></label><label>Descripción<textarea name="body" rows="3" required>{{ $post->body }}</textarea></label><label>Reemplazar fotos <span class="label-hint">Hasta 4 imágenes</span><input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple></label><div class="publish-options"><label class="check"><input type="checkbox" name="is_published" value="1" @checked($post->is_published)> Publicar ahora</label><label class="check"><input type="checkbox" name="share_on_social" value="1" @checked($post->share_on_social)> Publicar en redes sociales</label></div><button class="button secondary">Guardar cambios</button></form></details>
          </div>
          <form method="POST" action="{{ route('posts.destroy', [$business, $post]) }}">@csrf @method('DELETE')<button class="plain-button">Eliminar</button></form>
        </article>
      @empty
        <p class="muted">Todavía no creaste publicaciones.</p>
      @endforelse
    </div>
    {{ $posts->links() }}
  </div>
</section>
@endsection
