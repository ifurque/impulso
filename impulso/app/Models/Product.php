<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['business_id', 'name', 'brand', 'model', 'description', 'photo', 'type', 'category', 'unit', 'quantity', 'price', 'duration', 'is_active'];
    protected function casts(): array { return ['quantity' => 'decimal:2', 'price' => 'decimal:2', 'is_active' => 'boolean']; }
}
