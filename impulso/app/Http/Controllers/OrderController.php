<?php

namespace App\Http\Controllers;

use App\Mail\OrderReceived;
use App\Models\Business;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    private const EARTH_RADIUS_KM = 6371;

    public function store(Request $request, Business $business)
    {
        $data = $request->validate([
            'items' => ['nullable', 'json'],
            'product_id' => ['nullable', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'delivery_method' => ['required', 'in:delivery,pickup'],
            'delivery_address' => ['nullable', 'required_if:delivery_method,delivery', 'string', 'max:500'],
            'delivery_notes' => ['nullable', 'string', 'max:500'],
            'customer_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'customer_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'payment_method' => ['required', 'in:efectivo,transferencia,qr,tarjeta'],
        ]);
        $itemsInput = !empty($data['items']) ? json_decode($data['items'], true) : [[
            'product_id' => $data['product_id'] ?? null,
            'quantity' => $data['quantity'] ?? null,
        ]];
        if (!is_array($itemsInput) || count($itemsInput) === 0 || count($itemsInput) > 50) {
            return back()->withErrors(['items' => 'Agrega al menos un producto al carrito.'])->withInput();
        }

        $productIds = collect($itemsInput)->pluck('product_id')->filter()->unique()->values();
        $products = $business->products()->where('is_active', true)->whereIn('id', $productIds)->get()->keyBy('id');
        $orderItems = [];
        foreach ($itemsInput as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);
            if (!$products->has($productId) || $quantity < 1 || $quantity > 20) {
                return back()->withErrors(['items' => 'Hay un producto inválido o una cantidad fuera de rango.'])->withInput();
            }
            $product = $products->get($productId);
            $unitPrice = (float) ($product->price ?? 0);
            $orderItems[] = [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $unitPrice * $quantity,
            ];
        }
        abort_if($data['delivery_method'] === 'delivery' && ! $business->delivery_enabled, 422, 'Este emprendimiento no está habilitado para entregas.');

        if ($data['delivery_method'] === 'delivery' && $business->delivery_latitude !== null && $business->delivery_longitude !== null) {
            if ($data['customer_latitude'] === null || $data['customer_longitude'] === null) {
                return back()->withErrors(['customer_location' => 'Necesitamos tu ubicación actual para comprobar si estás dentro del rango de entrega.'])->withInput();
            }

            $distance = $this->distanceInKilometers(
                (float) $business->delivery_latitude,
                (float) $business->delivery_longitude,
                (float) $data['customer_latitude'],
                (float) $data['customer_longitude'],
            );

            if ($distance > (float) $business->delivery_radius_km) {
                return back()->withErrors(['customer_location' => sprintf('La ubicación está a %.1f km y supera el rango de entrega de %d km.', $distance, $business->delivery_radius_km)])->withInput();
            }
        }

        $firstItem = $orderItems[0];
        $subtotal = collect($orderItems)->sum('subtotal');
        $deliveryCost = $data['delivery_method'] === 'delivery' ? (float) ($business->delivery_cost ?? 0) : 0;
        $total = $subtotal + $deliveryCost;

        $order = DB::transaction(function () use ($business, $data, $firstItem, $subtotal, $deliveryCost, $total, $orderItems) {
            $order = $business->orders()->create([
            'product_id' => $firstItem['product']->id,
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'],
            'delivery_method' => $data['delivery_method'],
            'delivery_address' => $data['delivery_address'] ?? null,
            'delivery_notes' => $data['delivery_notes'] ?? null,
            'quantity' => $firstItem['quantity'],
            'subtotal' => $subtotal,
            'delivery_cost' => $deliveryCost,
            'total' => $total,
            'payment_method' => $data['payment_method'],
            'status' => 'pending',
            ]);
            $order->items()->createMany(array_map(fn ($item) => [
                'product_id' => $item['product']->id,
                'product_name' => $item['product']->name,
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal'],
            ], $orderItems));
            return $order;
        });

        Mail::to($business->owner->email)->send(new OrderReceived($order));

        return back()->with('success', 'Pedido enviado correctamente. El emprendimiento recibió la solicitud y podrá revisarla desde su panel.');
    }

    private function distanceInKilometers(float $latitudeA, float $longitudeA, float $latitudeB, float $longitudeB): float
    {
        $latitudeDelta = deg2rad($latitudeB - $latitudeA);
        $longitudeDelta = deg2rad($longitudeB - $longitudeA);
        $a = sin($latitudeDelta / 2) ** 2
            + cos(deg2rad($latitudeA)) * cos(deg2rad($latitudeB)) * sin($longitudeDelta / 2) ** 2;

        return self::EARTH_RADIUS_KM * 2 * asin(min(1, sqrt($a)));
    }
}
