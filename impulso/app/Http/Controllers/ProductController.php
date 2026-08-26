<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function store(Request $request, Business $business)
    {
        abort_unless($business->canBeManagedBy(Auth::user()), 403);
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'description' => ['nullable', 'string', 'max:500'], 'photo' => ['nullable', 'image', 'max:5120'], 'type' => ['required', 'in:product,service'], 'price' => ['nullable', 'numeric', 'min:0'], 'duration' => ['nullable', 'integer', 'min:1']]);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('products', 'public');
        }
        $business->products()->create($data);
        return back()->with('success', 'Propuesta agregada a tu perfil.');
    }

    public function destroy(Business $business, $product)
    {
        abort_unless($business->canBeManagedBy(Auth::user()), 403);
        $business->products()->findOrFail($product)->delete();
        return back()->with('success', 'Propuesta eliminada.');
    }
}