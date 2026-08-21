<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['business_id', 'name', 'description', 'photo', 'type', 'price', 'duration', 'is_active'];
    protected function casts(): array { return ['price' => 'decimal:2', 'is_active' => 'boolean']; }
}