<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Categoría de gastos (ej. insumos, servicios) compartida para clasificar los Expense. */
class ExpenseCategory extends Model
{
    protected $fillable = ['name', 'slug'];
    public function expenses() { return $this->hasMany(Expense::class); }
}