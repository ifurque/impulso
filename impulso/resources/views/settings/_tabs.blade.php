<nav class="profile-tabs" aria-label="Mi perfil">
  <a class="{{ $active === 'profile' ? 'is-active' : '' }}" href="{{ route('user.profile') }}" @if($active === 'profile') aria-current="page" @endif>Datos personales</a>
  <a class="{{ $active === 'customization' ? 'is-active' : '' }}" href="{{ route('user.customization') }}" @if($active === 'customization') aria-current="page" @endif>Personalización</a>
  <a class="{{ $active === 'businesses' ? 'is-active' : '' }}" href="{{ route('business.panels') }}" @if($active === 'businesses') aria-current="page" @endif>Mis emprendimientos</a>
</nav>
