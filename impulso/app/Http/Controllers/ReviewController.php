<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

/** Reseñas de clientes sobre un emprendimiento. */
class ReviewController extends Controller
{
    /** Guarda una reseña (también de visitantes); un usuario con sesión solo puede opinar una vez. */
    public function store(Request $request, Business $business)
    {
        if ($request->user() && $business->reviews()->where('user_id', $request->user()->id)->exists()) {
            return back()->withErrors(['reviewer_name' => 'Ya dejaste una opinión sobre este emprendimiento.']);
        }

        $data = $request->validate([
            'reviewer_name' => ['required', 'string', 'max:100'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'body' => ['required', 'string', 'max:1000'],
        ]);
        $business->reviews()->create($data + ['user_id' => $request->user()?->id]);
        return back()->with('success', 'Gracias por compartir tu experiencia.');
    }
}