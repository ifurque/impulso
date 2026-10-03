@extends('layouts.app')
@section('content')
<section class="form-page wrap admin-page">
  @include('admin._tabs', ['active' => 'businesses'])
  <div class="dashboard-top"><div><p class="eyebrow">Administración · Emprendimientos</p><h1>Modificar emprendimiento</h1><p class="muted">{{ $business->name }}</p></div></div>
  <form method="POST" enctype="multipart/form-data" action="{{ route('admin.businesses.update', $business) }}" class="business-form admin-edit-form">
    @csrf @method('PUT')
    <div class="form-grid">
      <label>Propietario<select name="owner_id" required>@foreach($users as $user)<option value="{{ $user->id }}" @selected(old('owner_id', $business->owner_id) == $user->id)>{{ $user->name }} · {{ $user->email }}</option>@endforeach</select>@error('owner_id')<small class="error">{{ $message }}</small>@enderror</label>
      <label>Nombre<input name="name" value="{{ old('name', $business->name) }}" maxlength="120" required>@error('name')<small class="error">{{ $message }}</small>@enderror</label>
      <label>Categoría<input name="category" value="{{ old('category', $business->category) }}" maxlength="80"></label>
      <label>Ubicación<input name="location" value="{{ old('location', $business->location) }}" maxlength="160"></label>
      <label>Teléfono<input name="phone" value="{{ old('phone', $business->phone) }}" maxlength="40"></label>
      <label>Correo<input type="email" name="email" value="{{ old('email', $business->email) }}" maxlength="180"></label>
    </div>
    <label>Descripción<textarea name="description" rows="5" maxlength="2000">{{ old('description', $business->description) }}</textarea></label>
    <label class="admin-checkbox"><input type="checkbox" name="is_public" value="1" @checked(old('is_public', $business->is_public))> Emprendimiento visible públicamente</label>
    <div class="form-grid"><label>Foto de perfil<input type="file" name="profile_photo" accept="image/*">@error('profile_photo')<small class="error">{{ $message }}</small>@enderror</label><label>Portada<input type="file" name="cover_photo" accept="image/*">@error('cover_photo')<small class="error">{{ $message }}</small>@enderror</label></div>
    <div class="admin-actions"><button class="button" type="submit">Guardar cambios</button><a class="button secondary" href="{{ route('admin.businesses') }}">Cancelar</a></div>
  </form>
</section>
@endsection
