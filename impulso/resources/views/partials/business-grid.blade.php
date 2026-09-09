@forelse($businesses as $business)
  @php($palette = ($palettes ?? \App\Http\Controllers\BusinessController::PUBLIC_PALETTES)[$business->public_palette ?? 'mint'] ?? \App\Http\Controllers\BusinessController::PUBLIC_PALETTES['mint'])
  @php($businessPrimary = $business->public_primary_color ?: $palette['paper'])
  @php($businessSecondary = $business->public_secondary_color ?: $palette['accent'])
  <article class="business-card">
    <div class="business-cover" style="--business-cover-bg: {{ $businessPrimary }}; --business-cover-accent: {{ $businessSecondary }}; @if($business->cover_photo_url) background-image: linear-gradient(135deg, {{ $businessSecondary }}66, transparent 70%), url('{{ $business->cover_photo_url }}'); background-size: cover; background-position: center; @endif">
      <span class="logo-placeholder">
        @if($business->profile_photo_url)
          <img src="{{ $business->profile_photo_url }}" alt="" aria-hidden="true" data-fallback="{{ Str::substr($business->name, 0, 1) }}" onerror="this.parentElement.textContent=this.dataset.fallback">
        @else
          {{ Str::substr($business->name, 0, 1) }}
        @endif
      </span>
      <span class="tag">{{ $business->category }}</span>
    </div>
    <div class="business-body">
      <div class="rating">★ {{ $business->visible_reviews_count ? number_format($business->visible_rating, 1) : '0,0' }} ({{ $business->visible_reviews_count }})</div>
      <h2>{{ $business->name }}</h2>
      <p>{{ Str::limit($business->description, 110) }}</p>
      <small>⌖ {{ $business->location }} · {{ $business->products_count }} propuestas</small>
      <a class="text-link" href="{{ route('business.show', $business) }}">Ver emprendimiento <span>→</span></a>
    </div>
  </article>
@empty
  <div class="empty" style="grid-column: 1/-1;">
    <span>✦</span>
    <h2>No encontramos coincidencias.</h2>
    <p>Intenta con otros términos de búsqueda o categorías.</p>
  </div>
@endforelse
