@extends('layouts.app')
@section('content')
<section class="form-page wrap profile-settings-page">
  <nav class="profile-tabs" aria-label="Mi perfil">
    <a class="is-active" href="{{ route('user.profile') }}" aria-current="page">Datos personales</a>
    <a href="{{ route('user.customization') }}">Personalización</a>
  </nav>

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

    <div class="form-grid">
      <label>Nombre completo
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required maxlength="120" autocomplete="name">
        @error('name')<small class="error">{{ $message }}</small>@enderror
      </label>
      <label>Correo electrónico
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="180" autocomplete="email">
        @error('email')<small class="error">{{ $message }}</small>@enderror
      </label>
      <label>Teléfono
        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="40" autocomplete="tel">
        @error('phone')<small class="error">{{ $message }}</small>@enderror
      </label>
    </div>

    <fieldset class="profile-password-fields">
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