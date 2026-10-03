<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class BusinessPlusController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $businesses = Business::query()
            ->when($user->role !== 'superadmin', fn ($query) => $query
                ->where('owner_id', $user->id)
                ->orWhereHas('members', fn ($members) => $members
                    ->whereKey($user->id)
                    ->wherePivotIn('role', ['owner', 'administrator'])))
            ->latest('created_at')
            ->get();

        abort_if($businesses->isEmpty(), 403);

        if ($businesses->count() === 1) {
            return redirect()->route('business.plus.show', $businesses->first());
        }

        return view('business.plus.index', compact('businesses'));
    }

    public function show(Request $request, Business $business)
    {
        abort_unless($business->canBeManagedBy($request->user()), 403);

        return view('business.plus.show', compact('business'));
    }
}
