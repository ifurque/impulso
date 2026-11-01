@extends('layouts.app')
{{-- Vista de administración: edición de un usuario por parte del superadmin --}}
@section('content')
<section class="form-page wrap admin-page">
  {{-- Pestañas de navegación del panel de administración --}}
  @include('admin._tabs', ['active' => 'users'])
  {{-- Encabezado con el correo del usuario que se edita --}}
  <div class="dashboard-top"><div><p class="eyebrow">Administración · Usuarios</p><h1>Modificar usuario</h1><p class="muted">{{ $user->email }}</p></div></div>
  {{-- Formulario de edición (PUT) con subida de avatar --}}
  <form method="POST" enctype="multipart/form-data" action="{{ route('admin.users.update', $user) }}" class="business-form admin-edit-form">
    @csrf @method('PUT')
    {{-- Foto de perfil actual con vista previa --}}
    <div class="profile-settings-avatar"><div class="account-avatar" id="admin-user-avatar-preview">@if($user->avatar_url)<img src="{{ $user->avatar_url }}" alt="Foto de {{ $user->name }}">@else<span data-preview-fallback>{{ Str::substr($user->name, 0, 1) }}</span>@endif</div><label>Foto de perfil<input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" data-image-preview="#admin-user-avatar-preview" data-preview-alt="Vista previa de la foto de perfil"></label>@error('avatar')<small class="error">{{ $message }}</small>@enderror</div>
    {{-- Datos personales y de contacto --}}
    <div class="form-grid">
      <label>Nombre completo<input name="name" value="{{ old('name', $user->name) }}" maxlength="120" required>@error('name')<small class="error">{{ $message }}</small>@enderror</label>
      <label>Correo electrónico<input type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="180" required>@error('email')<small class="error">{{ $message }}</small>@enderror</label>
      <label>Teléfono<input name="phone" value="{{ old('phone', $user->phone) }}" maxlength="40"></label>
    </div>
    {{-- Rol: marca si el usuario es emprendedor --}}
    <label class="admin-checkbox"><input type="checkbox" name="is_entrepreneur" value="1" @checked(old('is_entrepreneur', $user->is_entrepreneur))> Perfil emprendedor</label>
    {{-- Acciones: guardar o cancelar --}}
    <div class="admin-actions"><button class="button" type="submit">Guardar cambios</button><a class="button secondary" href="{{ route('admin.users') }}">Cancelar</a></div>
  </form>
</section>
@endsection
