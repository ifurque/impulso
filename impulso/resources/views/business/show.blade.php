@extends('layouts.app')
@section('body_class', 'business-public')
@php($palette = $palettes[$business->public_palette ?? 'mint'] ?? $palettes['mint'])
@php($publicNavBg = $business->public_navbar_color ?: $palette['soft'])
@php($publicPostBg = $business->public_posts_background ?: $palette['soft'])
@push('head')
<style>
  body.business-public .nav {
    background: {{ $publicNavBg }};
  }
  body.business-public .public-posts article {
    background: {{ $publicPostBg }};
  }
</style>
@endpush
@section('content')
<section class="profile public-page public-background-{{ $business->public_background ?? 'plain' }} wrap" style="--public-accent: {{ $palette['accent'] }}; --public-soft: {{ $palette['soft'] }}; --public-ink: {{ $palette['ink'] }}; --public-paper: {{ $palette['paper'] }}; --public-line: {{ $palette['line'] }}; --public-post-bg: {{ $publicPostBg }};">
  <a class="back" href="{{ route('discover') }}">← Volver a explorar</a>
  <div class="profile-hero"{{ $business->cover_photo_url ? ' style="background-image: linear-gradient(90deg, rgba(24,37,34,.82), rgba(24,37,34,.2)), url(\'' . $business->cover_photo_url . '\'); background-size: cover; background-position: center; color: white;"' : '' }}>
    <div class="profile-logo">
      @if($business->profile_photo_url)
        <img src="{{ $business->profile_photo_url }}" alt="Logo de {{ $business->name }}">
      @else
        {{ Str::substr($business->name, 0, 1) }}
      @endif
    </div>
    <div>
      <p class="eyebrow">{{ $business->category }}</p>
      <h1>{{ $business->name }}</h1>
      <p class="muted">⌖ {{ $business->location }} · Creado por {{ $business->owner->name }}</p>
    </div>
    <div class="profile-rating">
      <strong>★ {{ $business->reviews->count() ? number_format($business->reviews->avg('rating'), 1) : '0,0' }}</strong>
      <span>({{ $business->reviews->count() }} {{ $business->reviews->count() === 1 ? 'opinión' : 'opiniones' }})</span>
    </div>
  </div>
  <div class="profile-grid">
    <div>
      <h2>Sobre el emprendimiento</h2>
      <p class="large-text">{{ $business->description }}</p>
      @include('business._posts')
      
      <h2>Productos y servicios</h2>
      <label for="public-product-search" style="display:block; margin: 0 0 10px;">
        Buscar productos o servicios
        <input id="public-product-search" class="search-input" placeholder="Ej. pan, litro, limpieza, asesoria">
      </label>
      <div class="product-list">
        @forelse($business->products as $product)
          <div data-public-product data-filter-text="{{ Str::lower($product->name.' '.($product->category ?? '').' '.($product->unit ?? '').' '.($product->type === 'product' ? 'producto' : 'servicio')) }}">
            @if($product->photo)
              <img class="product-photo" src="{{ asset('storage/'.$product->photo) }}" alt="Foto de {{ $product->name }}">
            @endif
            <span>{{ $product->type === 'product' ? 'Producto' : 'Servicio' }} · {{ $product->category ?: 'Sin categoria' }}</span>
            <strong>{{ $product->name }}</strong>
            <small>
              {{ $product->description }}
              @if($product->price)
                · $ {{ number_format($product->price, 0, ',', '.') }} / {{ $product->unit ?? 'unidad' }}
              @endif
            </small>
          </div>
        @empty
          <p class="muted">Próximamente encontrarás aquí sus propuestas.</p>
        @endforelse
      </div>

      <h2>Servicio y atención</h2>
      <div class="info-cards">
        <div class="info-card">
          <strong>Entrega</strong>
          <p>{{ $business->delivery_enabled ? 'Hace envíos a domicilio' : 'No realiza entregas' }}</p>
          @if($business->delivery_enabled)
            <small>{{ $business->delivery_radius_km }} km de radio · ${{ number_format($business->delivery_cost ?? 0, 0, ',', '.') }} envío</small>
          @endif
        </div>
        <div class="info-card">
          <strong>Pagos cliente</strong>
          <p>{{ $business->payment_methods_customer ? implode(', ', $business->payment_methods_customer) : 'No configurado aún' }}</p>
        </div>
        <div class="info-card">
          <strong>Pagos emprendedor</strong>
          <p>{{ $business->payment_methods_business ? implode(', ', $business->payment_methods_business) : 'No configurado aún' }}</p>
        </div>
      </div>

      <h2>Redes y contacto</h2>
      <div class="actions">
        @foreach($business->socialLinks as $link)
          <a class="text-link" target="_blank" href="{{ $link->url }}">{{ ucfirst($link->platform) }} ↗</a>
        @endforeach
      </div>

      <h2>Reseñas <span class="rating">★ {{ number_format($business->reviews->avg('rating') ?: 0, 1) }}</span></h2>
      <div class="review-list">
        @forelse($business->reviews as $review)
          <article>
            <div class="rating">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
            <strong>{{ $review->reviewer_name }}</strong>
            <p>{{ $review->body }}</p>
          </article>
        @empty
          <p class="muted">Sé la primera persona en dejar una reseña.</p>
        @endforelse
      </div>
    </div>

    <aside class="contact-box">
      @if($business->appointments_enabled)
      <h3>Solicitar turno</h3>
      <form method="POST" action="{{ route('appointments.store', $business) }}" class="form" id="appointment-form">
        @csrf
        @guest
          <label>Nombre<input name="guest_name" required maxlength="100"></label>
          <label>Email para confirmar<input name="guest_email" type="email" required maxlength="180"></label>
        @endguest
        <label>
          Servicio
          <select name="product_id">
            <option value="">Selecciona una propuesta</option>
            @foreach($business->products as $product)
              <option value="{{ $product->id }}">{{ $product->name }} @if($product->price)· ${{ number_format($product->price, 0, ',', '.') }} / {{ $product->unit ?? 'unidad' }}@endif</option>
            @endforeach
          </select>
        </label>
        <label>Fecha<input name="appointment_date" id="appointment-date" type="date" min="{{ now()->format('Y-m-d') }}" required></label>
        <label>Hora
          <select name="start_time" id="appointment-time" required disabled>
            <option value="">Elegí primero una fecha</option>
          </select>
        </label>
        <label>Notas<textarea name="notes" rows="2"></textarea></label>
        <button class="button full">Solicitar turno <span>→</span></button>
        <small class="muted">Te enviaremos un enlace para confirmar el turno por email.</small>
      </form>
      <p class="muted appointment-empty" id="appointment-empty" hidden>No hay horarios disponibles para esta fecha.</p>
      @else
        <p class="muted">Este emprendimiento todavía no tiene habilitada la agenda de turnos.</p>
      @endif

      <hr>

      <h3>Dejar una reseña</h3>
      <form method="POST" action="{{ route('reviews.store', $business) }}" class="form">
        @csrf
        <label>Tu nombre<input name="reviewer_name" maxlength="100" required></label>
        <label>
          Calificación
          <select name="rating" required>
            <option value="5">★★★★★ Excelente</option>
            <option value="4">★★★★ Muy buena</option>
            <option value="3">★★★ Buena</option>
            <option value="2">★★ Regular</option>
            <option value="1">★ Necesita mejorar</option>
          </select>
        </label>
        <label>Tu reseña<textarea name="body" rows="4" maxlength="1000" required placeholder="Comparte tu experiencia"></textarea></label>
        <button class="button secondary full">Publicar reseña</button>
      </form>

      <hr>

      <h3>Hacer una consulta</h3>
      @auth
        <form method="POST" action="{{ route('inquiries.store', $business) }}" class="form">
          @csrf
          <label>Asunto<input name="subject" required></label>
          <label>Mensaje<textarea name="message" rows="3" required></textarea></label>
          <button class="button secondary full">Enviar consulta</button>
        </form>
      @else
        <p class="muted">Ingresa para enviar una consulta al emprendimiento.</p>
        <a class="button full" href="{{ route('login') }}">Ingresar</a>
      @endauth

      @if($business->products->where('is_active', true)->isNotEmpty())
        <hr>
        <h3>Hacer un pedido</h3>
        @if($business->delivery_enabled)<p class="muted">Podés retirarlo o pedir entrega en {{ $business->delivery_radius_km }} km · Costo ${{ number_format($business->delivery_cost ?? 0, 0, ',', '.') }}</p>@else<p class="muted">Retirá tu pedido directamente en el emprendimiento.</p>@endif
        <form method="POST" action="{{ route('orders.store', $business) }}" class="form" id="order-form" data-business-latitude="{{ $business->delivery_latitude }}" data-business-longitude="{{ $business->delivery_longitude }}" data-delivery-radius="{{ $business->delivery_radius_km }}">
          @csrf
          @if($errors->has('customer_location'))<div class="notice error-box">{{ $errors->first('customer_location') }}</div>@endif
          <label>
            Producto
            <select name="product_id" required>
              <option value="">Seleccioná un producto</option>
              @foreach($business->products as $product)
                @if($product->is_active)
                  <option value="{{ $product->id }}">{{ $product->name }} · ${{ number_format($product->price ?? 0, 0, ',', '.') }} / {{ $product->unit ?? 'unidad' }}</option>
                @endif
              @endforeach
            </select>
          </label>
          <label>Cantidad<input type="number" name="quantity" min="1" value="1" required></label>
          <label>Tu nombre<input name="customer_name" required maxlength="120"></label>
          <label>Teléfono<input name="customer_phone" required maxlength="40"></label>
          <label>Método de entrega<select name="delivery_method" id="delivery-method" required><option value="pickup">Retirar en el emprendimiento</option>@if($business->delivery_enabled)<option value="delivery">Envío a domicilio</option>@endif</select></label>
          <div id="delivery-fields" hidden>
            <label>Dirección de entrega<textarea name="delivery_address" rows="2"></textarea></label>
            <label>Notas del envío<textarea name="delivery_notes" rows="2"></textarea></label>
            <input type="hidden" name="customer_latitude" id="customer-latitude">
            <input type="hidden" name="customer_longitude" id="customer-longitude">
            <button type="button" class="button secondary" id="check-delivery-coverage">Comprobar si llegamos a tu ubicación</button>
            <p class="muted" id="delivery-coverage-status">Necesitamos tu ubicación actual para confirmar la cobertura.</p>
          </div>
          <label>
            Método de pago
            <select name="payment_method" required>
              <option value="">Elegí un método</option>
              @foreach($business->payment_methods_customer ?? ['efectivo', 'transferencia', 'qr'] as $method)
                <option value="{{ $method }}">{{ ucfirst(str_replace('_', ' ', $method)) }}</option>
              @endforeach
            </select>
          </label>
          <button class="button full">Confirmar pedido</button>
        </form>
      @endif
    </aside>
  </div>
</section>
@if($business->appointments_enabled)
<script>
  const appointmentDate = document.querySelector('#appointment-date');
  const appointmentTime = document.querySelector('#appointment-time');
  const appointmentEmpty = document.querySelector('#appointment-empty');
  const slotsUrl = @json(route('appointments.available', $business));

  appointmentDate.addEventListener('change', async () => {
    appointmentTime.disabled = true;
    appointmentTime.innerHTML = '<option value="">Buscando horarios...</option>';
    appointmentEmpty.hidden = true;

    const response = await fetch(`${slotsUrl}?date=${encodeURIComponent(appointmentDate.value)}`);
    const data = await response.json();
    appointmentTime.innerHTML = '<option value="">Seleccioná un horario</option>';
    data.slots.forEach(slot => appointmentTime.add(new Option(slot, slot)));
    appointmentTime.disabled = data.slots.length === 0;
    appointmentEmpty.hidden = data.slots.length > 0;
  });
</script>
@endif
<script>
  const publicProductSearch = document.querySelector('#public-product-search');
  const publicProductCards = document.querySelectorAll('[data-public-product]');
  publicProductSearch?.addEventListener('input', () => {
    const search = publicProductSearch.value.toLowerCase().trim();
    publicProductCards.forEach((card) => {
      const text = card.dataset.filterText || '';
      card.style.display = text.includes(search) ? '' : 'none';
    });
  });

  const deliveryMethod = document.querySelector('#delivery-method');
  const deliveryFields = document.querySelector('#delivery-fields');
  if (deliveryMethod && deliveryFields) {
    const updateDeliveryFields = () => { deliveryFields.hidden = deliveryMethod.value !== 'delivery'; };
    deliveryMethod.addEventListener('change', updateDeliveryFields);
    updateDeliveryFields();
  }

  const orderForm = document.querySelector('#order-form');
  const coverageButton = document.querySelector('#check-delivery-coverage');
  const coverageStatus = document.querySelector('#delivery-coverage-status');
  const customerLatitude = document.querySelector('#customer-latitude');
  const customerLongitude = document.querySelector('#customer-longitude');
  coverageButton?.addEventListener('click', () => {
    if (!navigator.geolocation) {
      coverageStatus.textContent = 'Este navegador no permite obtener la ubicación.';
      return;
    }
    coverageStatus.textContent = 'Calculando cobertura...';
    navigator.geolocation.getCurrentPosition((position) => {
      const latitude = position.coords.latitude;
      const longitude = position.coords.longitude;
      customerLatitude.value = latitude;
      customerLongitude.value = longitude;
      const businessLatitude = Number(orderForm.dataset.businessLatitude);
      const businessLongitude = Number(orderForm.dataset.businessLongitude);
      const radius = Number(orderForm.dataset.deliveryRadius);
      if (!Number.isFinite(businessLatitude) || !Number.isFinite(businessLongitude)) {
        coverageStatus.textContent = 'El emprendimiento todavía no configuró la ubicación exacta del local.';
        return;
      }
      const toRadians = (value) => value * Math.PI / 180;
      const latitudeDelta = toRadians(latitude - businessLatitude);
      const longitudeDelta = toRadians(longitude - businessLongitude);
      const a = Math.sin(latitudeDelta / 2) ** 2 + Math.cos(toRadians(businessLatitude)) * Math.cos(toRadians(latitude)) * Math.sin(longitudeDelta / 2) ** 2;
      const distance = 6371 * 2 * Math.asin(Math.min(1, Math.sqrt(a)));
      coverageStatus.textContent = distance <= radius
        ? `Sí, estás dentro del rango (${distance.toFixed(1)} km).`
        : `No, estás fuera del rango (${distance.toFixed(1)} km de ${radius} km).`;
    }, () => {
      coverageStatus.textContent = 'No pudimos obtener tu ubicación. Revisa el permiso del navegador.';
    }, { enableHighAccuracy: true, timeout: 10000 });
  });
</script>
@endsection