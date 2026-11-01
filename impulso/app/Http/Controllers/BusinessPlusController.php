<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

/**
 * Pantallas del plan +Impulso para los emprendimientos que administra el usuario.
 */
class BusinessPlusController extends Controller
{
    /** Lista los emprendimientos del usuario (todos si es superadmin); si hay uno solo, redirige directo a su pantalla. */
    public function index(Request $request)
    {
        $user = $request->user();
        $businesses = Business::query()
            ->when($user->role !== 'superadmin', fn ($query) => $query
                ->where('owner_id', $user->id)
                ->orWhereHas('members', fn ($members) => $members
                    ->whereKey($user->id)
                    ->whereIn('business_members.role', ['owner', 'administrator'])))
            ->latest('created_at')
            ->get();

        abort_if($businesses->isEmpty(), 403);

        if ($businesses->count() === 1) {
            return redirect()->route('business.plus.show', $businesses->first());
        }

        return view('business.plus.index', compact('businesses'));
    }

    /** Detalle del plan +Impulso de un emprendimiento. */
    public function show(Request $request, Business $business)
    {
        abort_unless($business->canBeManagedBy($request->user()), 403);

        return view('business.plus.show', compact('business'));
    }
}
