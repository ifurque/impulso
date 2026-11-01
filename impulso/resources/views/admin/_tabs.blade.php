{{-- Parcial: pestañas del panel de administración (Emprendimientos / Usuarios); $active marca la pestaña actual --}}
<nav class="profile-tabs admin-tabs" aria-label="Administración">
  <a class="{{ $active === 'businesses' ? 'is-active' : '' }}" href="{{ route('admin.businesses') }}" @if($active === 'businesses') aria-current="page" @endif>Emprendimientos</a>
  <a class="{{ $active === 'users' ? 'is-active' : '' }}" href="{{ route('admin.users') }}" @if($active === 'users') aria-current="page" @endif>Usuarios</a>
</nav>
