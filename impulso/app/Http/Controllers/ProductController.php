<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    private const PRODUCT_UNITS = ['unidad', 'kilo', 'litro'];
    private const TEXT_MAX_120 = 'max:120';

    private function productRules(): array
    {
        return [
            'name' => ['required', 'string', self::TEXT_MAX_120],
            'brand' => ['nullable', 'string', self::TEXT_MAX_120],
            'model' => ['nullable', 'string', self::TEXT_MAX_120],
            'category' => ['nullable', 'string', 'max:100'],
            'unit' => ['required', 'in:'.implode(',', self::PRODUCT_UNITS)],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function store(Request $request, Business $business)
    {
        abort_unless($business->canBeManagedBy(Auth::user()), 403);
        $data = $request->validate($this->productRules());
        $data['type'] = 'product';
        $data['is_active'] = true;
        $business->products()->create($data);
        return back()->with('success', 'Registro guardado en la base de datos.');
    }

    public function addStock(Request $request, Business $business)
    {
        abort_unless($business->canBeManagedBy(Auth::user()), 403);

        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'incoming_quantity' => ['required', 'numeric', 'min:0.01'],
        ]);

        $product = $business->products()->findOrFail($data['product_id']);
        $product->increment('quantity', (float) $data['incoming_quantity']);

        return back()->with('success', 'Mercadería ingresada y sumada al stock existente.');
    }

    public function update(Request $request, Business $business, $product)
    {
        abort_unless($business->canBeManagedBy(Auth::user()), 403);
        $data = $request->validate($this->productRules());

        $business->products()->findOrFail($product)->update($data + ['type' => 'product']);

        return back()->with('success', 'Registro actualizado.');
    }

    public function destroy(Business $business, $product)
    {
        abort_unless($business->canBeManagedBy(Auth::user()), 403);
        $business->products()->findOrFail($product)->delete();
        return back()->with('success', 'Propuesta eliminada.');
    }
}
