<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public function index(Business $business)
    {
        $this->ownerOnly($business);
        return view('movements.index', [
            'business' => $business->load('products'),
            'expenses' => $business->expenses()->with('category')->latest('expense_date')->paginate(10, ['*'], 'expenses_page'),
            'incomes' => $business->incomes()->with('product')->latest('income_date')->paginate(10, ['*'], 'incomes_page'),
        ]);
    }

    public function store(Request $request, Business $business)
    {
        $this->ownerOnly($business);
        $data = $request->validate(['description' => ['required', 'string', 'max:160'], 'amount' => ['required', 'numeric', 'min:0'], 'income_date' => ['required', 'date'], 'source' => ['required', 'in:sale,service,other'], 'product_id' => ['nullable', 'exists:products,id']]);
        $business->incomes()->create($data + ['created_by' => Auth::id()]);
        return back()->with('success', 'Pago entrante registrado.');
    }

    private function ownerOnly(Business $business): void
    {
        abort_unless($business->owner_id === Auth::id() || Auth::user()?->role === 'superadmin', 403);
    }
}