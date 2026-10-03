@extends('layouts.app')
@section('content')
<section class="form-page wrap admin-page">
  @include('admin._tabs', ['active' => 'users'])
  <div class="dashboard-top"><div><p class="eyebrow">Panel de administración</p><h1>Usuarios</h1><p class="muted">{{ $users->total() }} registrados</p></div></div>
  <div class="admin-list">
    @forelse($users as $user)
      <article class="admin-row">
        <span class="admin-avatar">@if($user->avatar_url)<img src="{{ $user->avatar_url }}" alt="Foto de {{ $user->name }}">@else{{ Str::substr($user->name, 0, 1) }}@endif</span>
        <div class="admin-copy">
          <div class="admin-name"><strong>{{ $user->name }}</strong>@if($user->role === 'superadmin')<span class="admin-role">Admin</span>@endif</div>
          <p class="admin-meta">{{ $user->email }} <span>·</span> {{ $user->phone ?: 'Sin teléfono' }}</p>
          <p class="admin-submeta">{{ $user->is_entrepreneur ? 'Perfil emprendedor' : 'Cliente' }} <span>·</span> {{ $user->owned_businesses_count }} emprendimientos <span>·</span> Alta {{ $user->created_at->format('d/m/Y') }}</p>
        </div>
        <div class="admin-actions">
          <a class="button secondary button-small" href="{{ route('admin.users.edit', $user) }}">Modificar</a>
          @if(!$user->is(auth()->user()))
            <form method="POST" action="{{ route('admin.users.admin', $user) }}">@csrf @method('PATCH')<button class="button secondary button-small admin-action-primary" type="submit">{{ $user->role === 'superadmin' ? 'Quitar Admin' : 'Hacer Admin' }}</button></form>
            <form method="POST" action="{{ route('admin.users.delete', $user) }}" onsubmit="return confirm('¿Eliminar definitivamente este usuario y sus emprendimientos?')">@csrf @method('DELETE')<button class="button danger button-small" type="submit">Eliminar</button></form>
          @endif
        </div>
      </article>
    @empty
      <p class="empty">Todavía no hay usuarios.</p>
    @endforelse
  </div>
  {{ $users->links() }}
</section>
@endsection
