@extends('layouts.app')
@section('content')
@include('partials.help-tour-trigger', ['label' => 'Guía de mi perfil', 'steps' => [
  ['target' => '[data-help-target="profile-identity"]', 'title' => 'Datos personales', 'body' => 'Completa nombre y apellido por separado y tu DNI. El DNI es único: no puede repetirse en otra cuenta.'],
  ['target' => '[data-help-target="profile-contact"]', 'title' => 'Verificar contacto', 'body' => 'Usa Verificar correo o Verificar teléfono para recibir un enlace. No puedes verificarte tú mismo: solo se confirma al abrir el enlace recibido. Al verificarse aparece un ✓.'],
  ['target' => '[data-help-target="profile-address"]', 'title' => 'Dirección', 'body' => 'Indica dirección, código postal, ciudad y provincia. Puedes completar estos datos usando tu ubicación actual.'],
  ['target' => '[data-help-target="profile-save"]', 'title' => 'Guardar', 'body' => 'Los cambios solo se aplican al guardar. Si cambias correo o teléfono, deberás verificarlos otra vez.'],
]])
<section class="form-page wrap profile-settings-page">
  @include('settings._tabs', ['active' => 'profile'])

  <div class="dashboard-top profile-settings-heading">
    <div>
      <p class="eyebrow">Tu cuenta</p>
      <h1>Mi perfil</h1>
      <p class="muted">Actualiza tu foto y los datos que quieras cambiar. No necesitas ingresar tu contraseña.</p>
    </div>
  </div>

  <form method="POST" enctype="multipart/form-data" action="{{ route('user.profile.update') }}" class="business-form profile-settings-form" autocomplete="off">
    @csrf
    <div class="profile-settings-avatar">
      <div class="account-avatar" id="account-avatar-preview">
        @if($user->avatar)
          <img src="{{ $user->avatar_url }}" alt="Foto de perfil de {{ $user->name }}">
        @else
          <span>{{ Str::upper(Str::substr($user->name, 0, 1)) }}</span>
        @endif
      </div>
      <label class="button secondary account-avatar-upload">Cambiar foto
        <input type="file" name="avatar" id="account-avatar-input" accept="image/*">
      </label>
      <small class="muted">JPG, PNG o WEBP, hasta 5 MB.</small>
      @error('avatar')<small class="error">{{ $message }}</small>@enderror
    </div>

    <div class="form-grid" data-help-target="profile-identity">
      @php [$defaultFirstName, $defaultLastName] = $user->nameParts(); @endphp
      <label>Nombre
        <input type="text" name="first_name" value="{{ old('first_name', $defaultFirstName) }}" maxlength="80" autocomplete="given-name">
        @error('first_name')<small class="error">{{ $message }}</small>@enderror
      </label>
      <label>Apellido
        <input type="text" name="last_name" value="{{ old('last_name', $defaultLastName) }}" maxlength="80" autocomplete="family-name">
        @error('last_name')<small class="error">{{ $message }}</small>@enderror
      </label>
      <label>DNI
        <input type="text" name="document_number" value="{{ old('document_number', $user->document_number) }}" maxlength="12" inputmode="numeric" autocomplete="off" placeholder="Solo números">
        @error('document_number')<small class="error">{{ $message }}</small>@enderror
      </label>
    </div>

    <div class="form-grid" data-help-target="profile-contact">
      <div class="verify-field">
        <label>Correo electrónico
          <input type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="180" autocomplete="email">
          @error('email')<small class="error">{{ $message }}</small>@enderror
        </label>
        <div class="verify-status">
          @if($user->hasVerifiedEmail())
            <span class="verify-badge" title="Correo verificado"><span aria-hidden="true">✓</span> Verificado</span>
          @else
            <span class="verify-pending">Sin verificar</span>
            <button class="button secondary button-small" type="submit" form="verify-email-form">Verificar correo</button>
          @endif
        </div>
      </div>
      <div class="verify-field">
        <label>Teléfono
          <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="40" autocomplete="tel">
          @error('phone')<small class="error">{{ $message }}</small>@enderror
        </label>
        <div class="verify-status">
          @if(blank($user->phone))
            <span class="verify-pending">Guarda un teléfono para poder verificarlo</span>
          @elseif($user->hasVerifiedPhone())
            <span class="verify-badge" title="Teléfono verificado"><span aria-hidden="true">✓</span> Verificado</span>
          @else
            <span class="verify-pending">Sin verificar</span>
            <button class="button secondary button-small" type="submit" form="verify-phone-form">Verificar teléfono</button>
          @endif
        </div>
      </div>
      @error('verification')<small class="error wide">{{ $message }}</small>@enderror
      <p class="muted verify-hint wide">Te enviaremos un enlace por correo o mensaje de texto: la verificación solo se completa desde ese enlace. Si cambias el correo o el teléfono, deberás verificarlo de nuevo.</p>
    </div>

    <div class="form-grid" data-help-target="profile-address">
      <div class="profile-location-action wide">
        <button type="button" class="button secondary" id="set-profile-location">Usar mi ubicación actual</button>
        <p class="muted" id="profile-location-status" role="status" aria-live="polite">Completa dirección, código postal, ciudad y provincia automáticamente.</p>
      </div>
      <label class="wide">Dirección
        <input type="text" id="profile-address" name="address" value="{{ old('address', $user->address) }}" maxlength="180" autocomplete="street-address" placeholder="Calle, número, piso y departamento">
        @error('address')<small class="error">{{ $message }}</small>@enderror
      </label>
      <label>Código postal
        <input type="text" id="profile-postal-code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" maxlength="10" autocomplete="postal-code">
        @error('postal_code')<small class="error">{{ $message }}</small>@enderror
      </label>
      <label>Ciudad
        <input type="text" id="profile-city" name="city" value="{{ old('city', $user->city) }}" maxlength="120" autocomplete="address-level2">
        @error('city')<small class="error">{{ $message }}</small>@enderror
      </label>
      <label>Provincia
        <select id="profile-province" name="province" autocomplete="address-level1">
          <option value="">Selecciona una provincia</option>
          @foreach(\App\Support\ArgentinaProvinces::all() as $province)
            <option value="{{ $province }}" @selected(old('province', $user->province) === $province)>{{ $province }}</option>
          @endforeach
        </select>
        @error('province')<small class="error">{{ $message }}</small>@enderror
      </label>
    </div>

    <button class="button" type="submit" data-help-target="profile-save">Guardar cambios <span>→</span></button>
  </form>
  <form id="verify-email-form" method="POST" action="{{ route('verification.send') }}" hidden>@csrf</form>
  <form id="verify-phone-form" method="POST" action="{{ route('phone.verification.send') }}" hidden>@csrf</form>
</section>
<script>
  document.getElementById('account-avatar-input')?.addEventListener('change', (event) => {
    const file = event.target.files?.[0];
    if (!file) return;
    const preview = document.getElementById('account-avatar-preview');
    const image = document.createElement('img');
    image.alt = 'Vista previa de tu foto de perfil';
    image.src = URL.createObjectURL(file);
    preview.replaceChildren(image);
  });

  const profileLocationButton = document.getElementById('set-profile-location');
  const profileLocationStatus = document.getElementById('profile-location-status');
  profileLocationButton?.addEventListener('click', () => {
    if (!navigator.geolocation) {
      profileLocationStatus.textContent = 'Este navegador no permite obtener la ubicación.';
      return;
    }

    profileLocationStatus.textContent = 'Obteniendo ubicación...';
    navigator.geolocation.getCurrentPosition((position) => {
      const { latitude, longitude } = position.coords;
      fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`, {
        headers: { 'Accept-Language': 'es' },
      })
        .then((response) => {
          if (!response.ok) throw new Error('No se pudo consultar la dirección.');
          return response.json();
        })
        .then((data) => {
          if (!data.address) {
            profileLocationStatus.textContent = 'No encontramos una dirección para esta ubicación. Puedes completar los datos manualmente.';
            return;
          }

          const address = data.address;
          const street = [address.road, address.house_number].filter(Boolean).join(' ');
          const addressInput = document.getElementById('profile-address');
          const postalCodeInput = document.getElementById('profile-postal-code');
          const cityInput = document.getElementById('profile-city');
          const provinceInput = document.getElementById('profile-province');
          const city = address.city || address.town || address.village || address.municipality || '';
          const province = (address.state || address.region || '').replace(/^Provincia de\s+/i, '').trim();

          if (street) addressInput.value = street;
          if (address.postcode) postalCodeInput.value = address.postcode;
          if (city) cityInput.value = city;
          if (province) {
            const provinceAliases = {
              'Ciudad de Buenos Aires': 'Ciudad Autónoma de Buenos Aires',
              'CABA': 'Ciudad Autónoma de Buenos Aires',
            };
            const provinceName = provinceAliases[province] || province;
            const provinceOption = Array.from(provinceInput.options)
              .find((option) => option.value.toLocaleLowerCase('es') === provinceName.toLocaleLowerCase('es'));
            if (provinceOption) provinceInput.value = provinceOption.value;
          }

          profileLocationStatus.textContent = 'Ubicación completada automáticamente. Puedes editar los datos manualmente.';
        })
        .catch(() => {
          profileLocationStatus.textContent = 'No pudimos completar la dirección. Puedes ingresar los datos manualmente.';
        });
    }, () => {
      profileLocationStatus.textContent = 'No pudimos obtener la ubicación. Revisa el permiso del navegador.';
    }, { enableHighAccuracy: true, timeout: 10000 });
  });
</script>
@endsection