@extends('layouts.app')
@section('content')
<section class="form-page wrap admin-page">
  @include('admin._tabs', ['active' => 'users'])
  <div class="dashboard-top"><div><p class="eyebrow">Administración · Usuarios</p><h1>Modificar usuario</h1><p class="muted">{{ $user->email }}</p></div></div>
  <form method="POST" enctype="multipart/form-data" action="{{ route('admin.users.update', $user) }}" class="business-form admin-edit-form">
    @csrf @method('PUT')
    <div class="profile-settings-avatar"><div class="account-avatar">@if($user->avatar_url)<img src="{{ $user->avatar_url }}" alt="Foto de {{ $user->name }}">@else{{ Str::substr($user->name, 0, 1) }}@endif</div><label>Foto de perfil<input type="file" name="avatar" accept="image/*"></label>@error('avatar')<small class="error">{{ $message }}</small>@enderror</div>
    <div class="form-grid">
      <label>Nombre completo<input name="name" value="{{ old('name', $user->name) }}" maxlength="120" required>@error('name')<small class="error">{{ $message }}</small>@enderror</label>
      <label>Correo electrónico<input type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="180" required>@error('email')<small class="error">{{ $message }}</small>@enderror</label>
      <label>Teléfono<input name="phone" value="{{ old('phone', $user->phone) }}" maxlength="40"></label>
    </div>
    <label class="admin-checkbox"><input type="checkbox" name="is_entrepreneur" value="1" @checked(old('is_entrepreneur', $user->is_entrepreneur))> Perfil emprendedor</label>
    <div class="admin-actions"><button class="button" type="submit">Guardar cambios</button><a class="button secondary" href="{{ route('admin.users') }}">Cancelar</a></div>
  </form>
</section>
@endsection
