@extends('layouts.app')

@section('content')
<section class="form-page wrap">
  <div class="section-heading">
    <p class="eyebrow">Nuevo emprendimiento</p>
    <h1>Cuentanos sobre tu idea.</h1>
    <p class="muted">Esta informacion ayudara a que las personas de tu comunidad te encuentren.</p>
  </div>

  <form method="POST" enctype="multipart/form-data" action="{{ route('business.store') }}" class="business-form">
    @csrf

    <div class="form-grid">
      <label>
        Nombre del emprendimiento
        <input name="name" value="{{ old('name') }}" placeholder="Ej. Taller La Esquina" required>
      </label>

      <label>
        Categoria
        <select name="category" required>
          <option value="">Selecciona una categoria</option>
          @foreach(['Gastronomia','Belleza y bienestar','Hogar y oficios','Moda y diseno','Servicios profesionales','Otro'] as $category)
            <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
          @endforeach
        </select>
      </label>

      <label class="wide">
        Descripcion
        <textarea name="description" rows="4" placeholder="Que haces y que te hace especial?" required>{{ old('description') }}</textarea>
      </label>

      <label>
        Telefono <span class="label-hint">opcional</span>
        <input name="phone" value="{{ old('phone') }}" placeholder="+54 9 ...">
      </label>

      <label>
        Correo de contacto <span class="label-hint">opcional</span>
        <input type="email" name="email" value="{{ old('email') }}">
      </label>

      <div class="location-field">
        <label>
          Ubicacion
          <input name="location" id="business-location" value="{{ old('location') }}" placeholder="Calle y número, ciudad, provincia" required>
        </label>
        <button type="button" class="button secondary location-button" id="set-business-location">Usar ubicación actual del local</button>
        <p class="muted" id="business-location-status">Podés escribirla manualmente o completar una ubicación aproximada.</p>
      </div>

      <input type="hidden" name="delivery_latitude" id="delivery-latitude" value="{{ old('delivery_latitude') }}">
      <input type="hidden" name="delivery_longitude" id="delivery-longitude" value="{{ old('delivery_longitude') }}">

      <label class="wide check">
        <input type="hidden" name="appointments_enabled" value="0">
        <input type="checkbox" id="appointments-enabled" name="appointments_enabled" value="1" @checked(old('appointments_enabled'))>
        <span>Quiero recibir turnos para este emprendimiento</span>
      </label>

      <div class="wide form-inline" id="appointments-fields">
        <label>
          Duracion de turnos
          <select name="appointment_slot_duration" id="appointment-slot-duration">
            <option value="15" @selected(old('appointment_slot_duration') == '15')>15 minutos</option>
            <option value="30" @selected(old('appointment_slot_duration', '30') == '30')>30 minutos</option>
            <option value="60" @selected(old('appointment_slot_duration') == '60')>1 hora</option>
          </select>
        </label>
      </div>

      <label class="wide check">
        <input type="hidden" name="delivery_enabled" value="0">
        <input type="checkbox" id="delivery-enabled" name="delivery_enabled" value="1" @checked(old('delivery_enabled'))>
        <span>Hago entregas a domicilio</span>
      </label>

      <div class="wide delivery-fields" id="delivery-fields">
        <div class="form-inline">
          <label>
            Radio de entrega (km)
            <input type="number" name="delivery_radius_km" id="delivery-radius" min="0" max="50" value="{{ old('delivery_radius_km', 0) }}">
          </label>
          <label>
            Costo de envio
            <input type="number" name="delivery_cost" id="delivery-cost" min="0" step="50" value="{{ old('delivery_cost', 0) }}">
          </label>
        </div>
        <p class="muted">Si activás las entregas, usaremos la ubicación del local indicada arriba para calcular la cobertura.</p>

        <div class="payment-row">
          <fieldset>
            <legend>Pagos cliente</legend>
            <div class="choice-list">
              @foreach(['efectivo','transferencia','tarjeta','mercado_pago','qr'] as $method)
                <label class="check small-check">
                  <input type="checkbox" name="customer_payment_methods[]" value="{{ $method }}" @checked(in_array($method, old('customer_payment_methods', [])))>
                  <span>{{ ucfirst(str_replace('_', ' ', $method)) }}</span>
                </label>
              @endforeach
            </div>
          </fieldset>

        </div>
      </div>

      <label>
        Logo o foto de perfil <span class="label-hint">JPG, PNG o WEBP · max. 5 MB</span>
        <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp">
      </label>

      <label>
        Foto de portada <span class="label-hint">JPG, PNG o WEBP · max. 8 MB</span>
        <input type="file" name="cover_photo" accept="image/jpeg,image/png,image/webp">
      </label>
    </div>

    @if($errors->any())
      <div class="notice error-box">Revisa los campos marcados antes de continuar.</div>
    @endif

    <button class="button">Publicar mi emprendimiento <span>→</span></button>
  </form>
</section>

<style>
  .location-field { display:grid; grid-template-columns:minmax(0, 1fr) auto; gap:10px 14px; align-items:end; grid-column:1 / -1; }
  .location-field .location-button { white-space:nowrap; margin-bottom:0; }
  .location-field #business-location-status { grid-column:1 / -1; margin:0; font-size:.78rem; }
  .form-grid { row-gap:14px; }
  .delivery-fields fieldset { border: 1px solid var(--line); border-radius: 8px; padding: 12px; }
  .delivery-fields legend { font-weight: 700; font-size: 0.9rem; }
  .choice-list { display: grid; gap: 8px; margin-top: 8px; }
  .small-check { margin: 0; }
  .payment-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; margin-top: 14px; }
  .starter-products { border: 1px solid var(--line); border-radius: 8px; padding: 16px; background: #fbfcfa; }
  .starter-products-head { display: flex; justify-content: space-between; align-items: flex-end; gap: 14px; margin-bottom: 14px; }
  .starter-products-head h2 { margin: 0 0 4px; font-size: 1.25rem; }
  .starter-products-list { display: grid; gap: 12px; }
  .starter-product-card { border: 1px solid var(--line); border-radius: 8px; background: #fff; padding: 14px; }
  .starter-product-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
  .section-disabled { opacity: 0.6; pointer-events: none; }
  @media (max-width: 760px) {
    .payment-row, .starter-product-grid, .location-field { grid-template-columns: 1fr; }
    .location-field .location-button { width:100%; }
    .starter-products { padding: 12px; }
    .starter-products-head { flex-direction: column; align-items: stretch; gap: 10px; }
    .starter-products-head .button { width: 100%; justify-content: center; }
    .starter-product-card { padding: 12px; }
    .remove-starter-product { width: 100%; text-align: left; padding-top: 8px; }
  }
</style>

<script>
  const deliveryEnabled = document.querySelector('#delivery-enabled');
  const deliveryFields = document.querySelector('#delivery-fields');
  const deliveryInputs = deliveryFields ? deliveryFields.querySelectorAll('input, select, textarea') : [];

  const appointmentsEnabled = document.querySelector('#appointments-enabled');
  const appointmentsFields = document.querySelector('#appointments-fields');
  const appointmentsInputs = appointmentsFields ? appointmentsFields.querySelectorAll('input, select, textarea') : [];

  const toggleSection = (isEnabled, section, inputs) => {
    if (!section) return;
    section.classList.toggle('section-disabled', !isEnabled);
    inputs.forEach((input) => {
      input.disabled = !isEnabled;
    });
  };

  const syncDelivery = () => toggleSection(deliveryEnabled.checked, deliveryFields, deliveryInputs);
  const syncAppointments = () => toggleSection(appointmentsEnabled.checked, appointmentsFields, appointmentsInputs);

  if (deliveryEnabled) {
    deliveryEnabled.addEventListener('change', syncDelivery);
    syncDelivery();
  }

  if (appointmentsEnabled) {
    appointmentsEnabled.addEventListener('change', syncAppointments);
    syncAppointments();
  }

  const locationButton = document.querySelector('#set-business-location');
  const locationStatus = document.querySelector('#business-location-status');
  const latitudeInput = document.querySelector('#delivery-latitude');
  const longitudeInput = document.querySelector('#delivery-longitude');
  const locationInput = document.querySelector('#business-location');
  locationButton?.addEventListener('click', () => {
    if (!navigator.geolocation) {
      locationStatus.textContent = 'Este navegador no permite obtener la ubicación.';
      return;
    }
    locationStatus.textContent = 'Obteniendo ubicación...';
    navigator.geolocation.getCurrentPosition((position) => {
      latitudeInput.value = position.coords.latitude;
      longitudeInput.value = position.coords.longitude;
      locationInput.value = `Ubicación actual (${position.coords.latitude.toFixed(5)}, ${position.coords.longitude.toFixed(5)})`;
      fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${position.coords.latitude}&lon=${position.coords.longitude}&zoom=18&addressdetails=1`, { headers: { 'Accept-Language': 'es' } })
        .then((response) => response.ok ? response.json() : null)
        .then((data) => {
          if (data?.address) {
            const address = data.address;
            const street = [address.road, address.house_number].filter(Boolean).join(' ');
            const city = address.city || address.town || address.village || address.municipality || '';
            const province = address.state || address.region || '';
            locationInput.value = [street, city, province].filter(Boolean).join(', ');
          }
          locationStatus.textContent = 'Ubicación completada automáticamente. También podés editarla manualmente.';
        })
        .catch(() => { locationStatus.textContent = 'Coordenadas guardadas. Podés editar la ubicación manualmente.'; });
    }, () => {
      locationStatus.textContent = 'No pudimos obtener la ubicación. Revisa el permiso del navegador.';
    }, { enableHighAccuracy: true, timeout: 10000 });
  });

</script>
@endsection
