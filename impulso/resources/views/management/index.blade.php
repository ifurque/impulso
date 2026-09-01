@extends('layouts.app')

@section('content')
<section class="dashboard wrap">
  <div class="dashboard-top">
    <div><p class="eyebrow">Administración</p><h1>Gestion operativa.</h1><p class="muted">Administra productos, pedidos, turnos y consultas desde un solo lugar.</p></div>
    <a class="button secondary" href="{{ route('dashboard', $business) }}">Volver al panel</a>
  </div>

  <section class="panel management-panel" style="margin-bottom: 22px;">
    <p class="eyebrow">Catalogo</p>
    <h2>Productos y servicios</h2>
    <div class="management-products-grid">
      <form method="POST" enctype="multipart/form-data" action="{{ route('products.store', $business) }}" class="form">
        @csrf
        <label>Tipo
          <select name="type" id="product-type" required>
            <option value="product">Producto</option>
            <option value="service">Servicio</option>
          </select>
        </label>
        <label>Nombre
          <input name="name" maxlength="120" required>
        </label>
        <label>Categoria
          <input name="category" maxlength="100" placeholder="Ej. Panificados, Limpieza, Consultoria">
        </label>
        <label>Unidad de medida
          <select name="unit" id="product-unit" required>
            <option value="unidad">Unidad</option>
            <option value="kilo">Kilo</option>
            <option value="litro">Litro</option>
          </select>
        </label>
        <label>Precio
          <input type="number" name="price" step="0.01" min="0" required>
        </label>
        <label>Descripcion <span class="label-hint">opcional</span>
          <textarea name="description" rows="2" maxlength="500"></textarea>
        </label>
        <label>Foto <span class="label-hint">opcional</span>
          <input type="file" name="photo" accept="image/jpeg,image/png,image/webp">
        </label>
        <button class="button">Agregar</button>
      </form>

      <div>
        <label for="catalog-search" style="display: block; margin-bottom: 8px;">Buscar en catalogo</label>
        <input id="catalog-search" class="search-input" placeholder="Buscar por nombre, categoria o tipo">
        <div class="stack-list" id="catalog-list" style="margin-top: 12px;">
          @forelse($business->products as $product)
            <article class="mini-item" data-product-card data-filter-text="{{ Str::lower($product->name.' '.($product->category ?? '').' '.($product->type === 'product' ? 'producto' : 'servicio')) }}">
              <div>
                <strong>{{ $product->name }}</strong>
                <small>{{ $product->type === 'product' ? 'Producto' : 'Servicio' }} · {{ $product->category ?: 'Sin categoria' }}</small>
                <small>Unidad: {{ ucfirst($product->unit ?? 'unidad') }} · $ {{ number_format($product->price ?? 0, 0, ',', '.') }}</small>
              </div>
              <form method="POST" action="{{ route('products.destroy', [$business, $product]) }}" onsubmit="return confirm('¿Eliminar este item del catalogo?')">
                @csrf
                @method('DELETE')
                <button class="plain-button">Eliminar</button>
              </form>
            </article>
          @empty
            <p class="muted">Todavia no hay productos o servicios cargados.</p>
          @endforelse
        </div>
      </div>
    </div>
  </section>

  <div class="management-grid {{ $business->delivery_enabled ? 'has-delivery' : '' }}">
    @if($business->delivery_enabled)
      <section class="panel management-panel">
        <p class="eyebrow">Pedidos con envío</p><h2>Entregas a domicilio</h2>
        <div class="stack-list">@forelse($deliveryOrders as $order)<div class="mini-item"><div><strong>{{ $order->customer_name }}</strong><small>{{ $order->product?->name ?? 'Producto' }} · {{ $order->quantity }} unidad(es)</small><small>{{ $order->delivery_address }}</small></div><div class="meta"><span>{{ ucfirst($order->payment_method) }}</span><strong>$ {{ number_format($order->total, 0, ',', '.') }}</strong></div><form method="POST" action="{{ route('orders.status', [$business, $order]) }}" class="inline-form">@csrf<select name="status"><option value="pending" @selected($order->status === 'pending')>Pendiente</option><option value="confirmed" @selected($order->status === 'confirmed')>Confirmado</option><option value="delivered" @selected($order->status === 'delivered')>Entregado</option><option value="cancelled" @selected($order->status === 'cancelled')>Cancelado</option></select><button class="plain-button">Guardar</button></form></div>@empty<p class="muted">Todavía no hay pedidos con envío.</p>@endforelse</div>
      </section>
    @endif

    <section class="panel management-panel">
      <p class="eyebrow">Pedidos sin envío</p><h2>Retiros en el emprendimiento</h2>
      <div class="stack-list">@forelse($pickupOrders as $order)<div class="mini-item"><div><strong>{{ $order->customer_name }}</strong><small>{{ $order->product?->name ?? 'Producto' }} · {{ $order->quantity }} unidad(es)</small><small>Retira en el emprendimiento</small></div><div class="meta"><span>{{ ucfirst($order->payment_method) }}</span><strong>$ {{ number_format($order->total, 0, ',', '.') }}</strong></div><form method="POST" action="{{ route('orders.status', [$business, $order]) }}" class="inline-form">@csrf<select name="status"><option value="pending" @selected($order->status === 'pending')>Pendiente</option><option value="confirmed" @selected($order->status === 'confirmed')>Confirmado</option><option value="delivered" @selected($order->status === 'delivered')>Retirado</option><option value="cancelled" @selected($order->status === 'cancelled')>Cancelado</option></select><button class="plain-button">Guardar</button></form></div>@empty<p class="muted">Todavía no hay pedidos para retirar.</p>@endforelse</div>
    </section>

    <section class="panel management-panel">
      <p class="eyebrow">Atención</p><h2>Turnos recientes</h2>
      <div class="expense-list">@forelse($appointments as $appointment)<div><span><strong>{{ $appointment->client?->name ?? $appointment->guest_name }} @if($appointment->product) · {{ $appointment->product->name }} @endif</strong><small>{{ $appointment->appointment_date->format('d/m/Y') }} a las {{ $appointment->start_time }} · {{ ucfirst($appointment->status) }}</small></span><form method="POST" action="{{ route('appointments.status', [$business, $appointment->id]) }}">@csrf<select name="status" onchange="this.form.submit()"><option value="pending" @selected($appointment->status === 'pending')>Pendiente</option><option value="confirmed" @selected($appointment->status === 'confirmed')>Confirmado</option><option value="completed" @selected($appointment->status === 'completed')>Completado</option><option value="cancelled" @selected($appointment->status === 'cancelled')>Cancelado</option></select></form></div>@empty<p class="muted">Todavía no hay turnos solicitados.</p>@endforelse</div>
    </section>

    <section class="panel management-panel">
      <p class="eyebrow">Mensajes</p><h2>Consultas de clientes</h2>
      <div class="stack-list">
        @forelse($inquiries as $inquiry)
          <article class="mini-item" style="display:block;">
            <div style="margin-bottom: 10px;">
              <strong>{{ $inquiry->client?->name ?? 'Cliente' }}</strong>
              <small>{{ $inquiry->subject }} · {{ $inquiry->created_at->format('d/m/Y H:i') }}</small>
              <small>{{ $inquiry->message }}</small>
            </div>
            <form method="POST" action="{{ route('inquiries.status', [$business, $inquiry->id]) }}" class="form" style="gap:10px;">
              @csrf
              <label>
                Respuesta
                <textarea name="response" rows="2" maxlength="1000" placeholder="Escribe una respuesta para el cliente">{{ old('response', $inquiry->response) }}</textarea>
              </label>
              <div class="inline-form">
                <select name="status">
                  <option value="pending" @selected($inquiry->status === 'pending')>Pendiente</option>
                  <option value="answered" @selected($inquiry->status === 'answered')>Respondida</option>
                  <option value="closed" @selected($inquiry->status === 'closed')>Cerrada</option>
                </select>
                <button class="plain-button">Guardar</button>
              </div>
            </form>
          </article>
        @empty
          <p class="muted">Todavía no hay consultas de clientes.</p>
        @endforelse
      </div>
    </section>
  </div>
</section>

<style>
  .management-products-grid {
    display: grid;
    grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
    gap: 16px;
  }
  @media (max-width: 900px) {
    .management-products-grid {
      grid-template-columns: 1fr;
    }
  }
  @media (max-width: 640px) {
    .management-panel .form .button { width: 100%; }
    .management-panel .mini-item { gap: 8px; padding: 12px 0; }
    .management-panel .mini-item form .plain-button { width: 100%; text-align: left; }
  }
</style>

<script>
  const productTypeField = document.querySelector('#product-type');
  const productUnitField = document.querySelector('#product-unit');
  const catalogSearchInput = document.querySelector('#catalog-search');
  const catalogCards = document.querySelectorAll('[data-product-card]');

  const syncUnitWithType = () => {
    if (!productTypeField || !productUnitField) return;
    if (productTypeField.value === 'service') {
      productUnitField.value = 'unidad';
      productUnitField.disabled = true;
      return;
    }
    productUnitField.disabled = false;
  };

  productTypeField?.addEventListener('change', syncUnitWithType);
  syncUnitWithType();

  catalogSearchInput?.addEventListener('input', () => {
    const search = catalogSearchInput.value.toLowerCase().trim();
    catalogCards.forEach((card) => {
      const text = card.dataset.filterText || '';
      card.style.display = text.includes(search) ? '' : 'none';
    });
  });
</script>
@endsection
