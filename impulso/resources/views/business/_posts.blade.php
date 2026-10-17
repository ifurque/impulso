@php($publishedProducts = $business->products->where('is_active', true))
@if($business->posts->count() || $publishedProducts->isNotEmpty())
    <div class="public-posts" style="--public-post-bg: {{ $publicPostBg ?? 'var(--public-soft)' }};">
        @foreach($publishedProducts as $product)
            <article class="stock-product-publication" data-public-product
                data-filter-text="{{ Str::lower($product->name . ' ' . ($product->brand ?? '') . ' ' . ($product->description ?? '') . ' producto') }}"
                style="background-color: {{ $publicPostBg ?? 'var(--public-soft)' }};">
                @if($product->photo)
                    <img class="post-public-photo" src="{{ $product->photo_url }}" alt="Foto de {{ $product->name }}"
                        loading="lazy">
                @endif
                <span class="tag">Producto</span>
                <h3>{{ $product->name }}</h3>
                @if($product->brand)
                    <p class="stock-publication-brand">{{ $product->brand }}</p>
                @endif
                @if($product->description)
                    <p>{{ $product->description }}</p>
                @endif
                @if($product->price !== null)
                    <strong>$ {{ number_format((float) $product->price, 0, ',', '.') }}</strong>
                @endif
                <small class="stock-publication-quantity">
                    Stock disponible: {{ number_format((float) ($product->quantity ?? 0), 2, ',', '.') }}
                    {{ $product->unit === 'kilo' ? 'Kg' : ($product->unit === 'litro' ? 'Lt' : 'unidad') }}
                </small>
                <button type="button" class="button secondary add-to-cart" data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}" data-product-price="{{ $product->price ?? 0 }}">Agregar al carrito
                    <span>+</span></button>
            </article>
        @endforeach

        @foreach($business->posts as $post)
            <article data-public-product
                data-filter-text="{{ Str::lower($post->title . ' ' . $post->body . ' ' . ($post->type === 'offer' ? 'oferta' : ($post->type === 'product' ? 'producto' : 'novedad'))) }}"
                style="background-color: {{ $publicPostBg ?? 'var(--public-soft)' }};">
                @if($post->photo)
                    <img class="post-public-photo" src="{{ $post->photo_url }}" alt="Foto de {{ $post->title }}">
                @endif
                <span
                    class="tag">{{ $post->type === 'offer' ? 'Oferta' : ($post->type === 'product' ? 'Producto' : 'Novedad') }}</span>
                <h3>{{ $post->title }}</h3>
                <p>{{ $post->body }}</p>
                @if($post->price)
                    <strong>$ {{ number_format($post->discount_price ?: $post->price, 0, ',', '.') }}</strong>
                    @if($post->discount_price)
                        <del>$ {{ number_format($post->price, 0, ',', '.') }}</del>
                        <span class="discount-badge">{{ $post->discountPercentage() }}% OFF</span>
                    @endif
                @endif
                @if($post->products->isNotEmpty())
                    <small class="stock-publication-quantity">Incluye: @foreach($post->products as $product){{ $product->name }}
                    x{{ $product->pivot->quantity }}{{ !$loop->last ? ', ' : '' }}@endforeach</small>
                @endif
            </article>
        @endforeach
    </div>
@endif