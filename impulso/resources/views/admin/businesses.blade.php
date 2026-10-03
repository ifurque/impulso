@extends('layouts.app')
@section('content')
<section class="form-page wrap admin-page">
  @include('admin._tabs', ['active' => 'businesses'])
  <div class="dashboard-top"><div><p class="eyebrow">Panel de administración</p><h1>Emprendimientos</h1><p class="muted">{{ $businesses->total() }} registrados</p></div></div>
  <div class="admin-list">
    @forelse($businesses as $business)
      <article class="admin-row">
        <span class="admin-avatar">@if($business->profile_photo_url)<img src="{{ $business->profile_photo_url }}" alt="Foto de {{ $business->name }}">@else{{ Str::substr($business->name, 0, 1) }}@endif</span>
        <div class="admin-copy">
          <div class="admin-name"><strong>{{ $business->name }}</strong>@if($business->is_plus)<span class="verified-badge" aria-label="Verificado por +Impulso" title="Verificado por +Impulso">✓</span>@endif</div>
          <p class="admin-meta">{{ $business->location ?: 'Sin zona' }} <span>·</span> {{ $business->category ?: 'Sin clasificación' }}</p>
          <p class="admin-submeta">{{ $business->email ?: 'Sin correo de contacto' }} <span>·</span> {{ $business->phone ?: 'Sin teléfono' }} <span>·</span> {{ $business->owner?->name ?: 'Sin propietario' }}</p>
        </div>
        <div class="admin-actions">
          <a class="button secondary button-small" href="{{ route('admin.businesses.edit', $business) }}">Modificar</a>
          <form method="POST" action="{{ route('admin.businesses.plus', $business) }}">@csrf @method('PATCH')<button class="button secondary button-small admin-action-primary" type="submit">{{ $business->is_plus ? 'Quitar +Impulso' : 'Dar +Impulso' }}</button></form>
          <form method="POST" action="{{ route('admin.businesses.delete', $business) }}" onsubmit="return confirm('¿Eliminar definitivamente este emprendimiento y sus datos?')">@csrf @method('DELETE')<button class="button danger button-small" type="submit">Eliminar</button></form>
        </div>
      </article>
    @empty
      <p class="empty">Todavía no hay emprendimientos.</p>
    @endforelse
  </div>
  {{ $businesses->links() }}
</section>
@endsection
