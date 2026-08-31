@extends('layouts.app')

@section('content')
<section class="dashboard wrap">
  <div class="dashboard-top">
    <div><p class="eyebrow">Finanzas de {{ $business->name }}</p><h1>Ingresos.</h1><p class="muted">Registra pagos y revisa todos los movimientos del negocio.</p></div>
    <div class="page-actions"><a class="button secondary" href="{{ route('dashboard', $business) }}">Volver al panel</a><a class="button" href="{{ route('expenses.index', $business) }}">Ir a egresos <span>→</span></a></div>
  </div>
  <div class="dashboard-grid">
    <div class="panel"><p class="eyebrow">Nuevo ingreso</p><h2>Registrar pago entrante</h2><form method="POST" action="{{ route('incomes.store', $business) }}" class="form">@csrf<label>Descripción<input name="description" placeholder="Ej. Venta del día" required></label><label>Origen<select name="source"><option value="sale">Venta</option><option value="service">Servicio</option><option value="other">Otro</option></select></label><label>Producto o servicio<select name="product_id"><option value="">Sin asociar</option>@foreach($business->products as $product)<option value="{{ $product->id }}">{{ $product->name }}</option>@endforeach</select></label><label>Monto<input name="amount" type="number" step="0.01" min="0" required></label><label>Fecha<input name="income_date" type="date" value="{{ now()->format('Y-m-d') }}" required></label><button class="button">Guardar ingreso <span>→</span></button></form></div>
    <div class="panel"><p class="eyebrow">Historial de ingresos</p><h2>Pagos recientes</h2><div class="expense-list">@forelse($incomes as $income)<div><span class="expense-icon income-icon">+</span><span><strong>{{ $income->description }}</strong><small>{{ $income->source === 'sale' ? 'Venta' : ($income->source === 'service' ? 'Servicio' : 'Otro') }} · {{ $income->income_date->format('d/m/Y') }}</small></span><b>$ {{ number_format($income->amount, 0, ',', '.') }}</b></div>@empty<p class="muted">Todavía no hay ingresos registrados.</p>@endforelse</div>{{ $incomes->links() }}</div>
  </div>
</section>
@endsection
