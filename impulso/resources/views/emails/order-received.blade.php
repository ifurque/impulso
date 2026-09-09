<h1>Nuevo pedido recibido</h1>
<p>Hola {{ $order->business->owner->name }}, recibiste un pedido para {{ $order->business->name }}.</p>
<p><strong>Cliente:</strong> {{ $order->customer_name }}</p>
<p><strong>Productos:</strong></p>
<ul>
	@forelse($order->items as $item)
		<li>{{ $item->quantity }} × {{ $item->product_name }} — ${{ number_format($item->subtotal, 0, ',', '.') }}</li>
	@empty
		<li>{{ $order->quantity }} × {{ $order->product?->name ?? 'Producto no encontrado' }}</li>
	@endforelse
</ul>
<p><strong>Dirección:</strong> {{ $order->delivery_address }}</p>
<p><strong>Pago:</strong> {{ ucfirst($order->payment_method) }}</p>
<p><strong>Total:</strong> ${{ number_format($order->total, 0, ',', '.') }}</p>
<p>Ingresá al panel para revisar y actualizar el estado del envío.</p>
