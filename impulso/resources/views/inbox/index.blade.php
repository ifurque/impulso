@extends('layouts.app')

@section('content')
<section class="dashboard wrap">
  <div class="dashboard-top">
    <div>
      <p class="eyebrow">Mi cuenta</p>
      <h1>Bandeja de consultas</h1>
      <p class="muted">Revisa tus mensajes enviados y las respuestas de los emprendimientos.</p>
    </div>
  </div>

  <section class="panel" style="max-width: 980px;">
    <div class="expense-list">
      @forelse($inquiries as $inquiry)
        <article class="inquiry-item">
          <header>
            <strong>{{ $inquiry->subject }}</strong>
            <small>{{ $inquiry->created_at->format('d/m/Y H:i') }} · {{ $inquiry->business?->name ?? 'Emprendimiento' }}</small>
          </header>
          <p><b>Tu consulta:</b> {{ $inquiry->message }}</p>
          @if($inquiry->response)
            <p><b>Respuesta del emprendimiento:</b> {{ $inquiry->response }}</p>
            <small class="muted">Respondida el {{ optional($inquiry->answered_at)->format('d/m/Y H:i') ?? 'sin fecha' }}</small>
          @else
            <p class="muted">Aun sin respuesta.</p>
          @endif
        </article>
      @empty
        <p class="muted">Todavia no enviaste consultas.</p>
      @endforelse
    </div>

    <div style="margin-top: 20px;">
      {{ $inquiries->links() }}
    </div>
  </section>
</section>
@endsection
