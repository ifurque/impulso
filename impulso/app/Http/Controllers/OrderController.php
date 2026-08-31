<?php

namespace App\Http\Controllers;

use App\Mail\OrderReceived;
use App\Models\Business;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function store(Request $request, Business $business)
    {
        abort_unless($business->delivery_enabled, 422, 'Este emprendimiento no está habilitado para entregas.');

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'delivery_address' => ['required', 'string', 'max:500'],
            'delivery_notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['required', 'in:efectivo,transferencia,qr,tarjeta'],
        ]);

        $product = $business->products()->whereKey($data['product_id'])->firstOrFail();

        $subtotal = (float) $product->price * (int) $data['quantity'];
        $deliveryCost = (float) ($business->delivery_cost ?? 0);
        $total = $subtotal + $deliveryCost;

        $order = $business->orders()->create([
            'product_id' => $product->id,
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'delivery_address' => $data['delivery_address'],
            'delivery_notes' => $data['delivery_notes'] ?? null,
            'quantity' => $data['quantity'],
            'subtotal' => $subtotal,
            'delivery_cost' => $deliveryCost,
            'total' => $total,
            'payment_method' => $data['payment_method'],
            'status' => 'pending',
        ]);

        Mail::to($business->owner->email)->send(new OrderReceived($order));

        return back()->with('success', 'Pedido enviado correctamente. El emprendimiento recibió la solicitud y podrá revisarla desde su panel.');
    }
}
