@extends('layouts.app')
@section('content')
@include('partials.help-tour-trigger', ['label' => 'Guía de mi perfil', 'steps' => [
  ['target' => '[data-help-target="profile-identity"]', 'title' => 'Datos personales', 'body' => 'Completa nombre y apellido por separado y tu DNI. El DNI es único: no puede repetirse en otra cuenta.'],
  ['target' => '[data-help-target="profile-contact"]', 'title' => 'Verificar contacto', 'body' => 'Usa Verificar correo o Verificar teléfono para recibir un enlace. No puedes verificarte tú mismo: solo se confirma al abrir el enlace recibido. Al verificarse aparece un ✓.'],
  ['target' => '[data-help-target="profile-address"]', 'title' => 'Dirección', 'body' => 'Indica dirección, código postal, ciudad y provincia.'],
  ['target' => '[data-help-target="profile-password"]', 'title' => 'Contraseña', 'body' => 'Déjala vacía si no quieres cambiarla.'],
  ['target' => '.profile-settings-form button[type="submit"]:last-of-type', 'title' => 'Guardar', 'body' => 'Los cambios solo se aplican al guardar. Si cambias correo o teléfono, deberás verificarlos otra vez.'],
]])
<section class="form-page wrap profile-settings-page">
  @include('settings._tabs', ['active' => 'profile'])

  <div class="dashboard-top profile-settings-heading">
    <div>
      <p class="eyebrow">Tu cuenta</p>
      <h1>Mi perfil</h1>
      <p class="muted">Mantén actualizados tus datos y la forma en que te presentas.</p>
    </div>
  </div>

  <form method="POST" enctype="multipart/form-data" action="{{ route('user.profile.update') }}" class="business-form profile-settings-form">
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
        <input type="text" name="first_name" value="{{ old('first_name', $defaultFirstName) }}" required maxlength="80" autocomplete="given-name">
        @error('first_name')<small class="error">{{ $message }}</small>@enderror
      </label>
      <label>Apellido
        <input type="text" name="last_name" value="{{ old('last_name', $defaultLastName) }}" required maxlength="80" autocomplete="family-name">
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
          <input type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="180" autocomplete="email">
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
      <label class="wide">Dirección
        <input type="text" name="address" value="{{ old('address', $user->address) }}" maxlength="180" autocomplete="street-address" placeholder="Calle, número, piso y departamento">
        @error('address')<small class="error">{{ $message }}</small>@enderror
      </label>
      <label>Código postal
        <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" maxlength="10" autocomplete="postal-code">
        @error('postal_code')<small class="error">{{ $message }}</small>@enderror
      </label>
      <label>Ciudad
        <input type="text" name="city" value="{{ old('city', $user->city) }}" maxlength="120" autocomplete="address-level2">
        @error('city')<small class="error">{{ $message }}</small>@enderror
      </label>
      <label>Provincia
        <select name="province" autocomplete="address-level1">
          <option value="">Selecciona una provincia</option>
          @foreach(\App\Support\ArgentinaProvinces::all() as $province)
            <option value="{{ $province }}" @selected(old('province', $user->province) === $province)>{{ $province }}</option>
          @endforeach
        </select>
        @error('province')<small class="error">{{ $message }}</small>@enderror
      </label>
    </div>

    <fieldset class="profile-password-fields" data-help-target="profile-password">
      <legend>Cambiar contraseña</legend>
      <p class="muted">Déjala vacía si no quieres cambiarla.</p>
      <div class="form-grid">
        <label>Contraseña actual
          <input type="password" name="current_password" autocomplete="current-password">
          @error('current_password')<small class="error">{{ $message }}</small>@enderror
        </label>
        <label>Nueva contraseña
          <input type="password" name="password" autocomplete="new-password">
          @error('password')<small class="error">{{ $message }}</small>@enderror
        </label>
        <label>Repetir nueva contraseña
          <input type="password" name="password_confirmation" autocomplete="new-password">
        </label>
      </div>
    </fieldset>

    <button class="button" type="submit">Guardar cambios <span>→</span></button>
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
</script>
@endsection