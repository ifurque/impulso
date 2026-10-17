<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostProduct extends Model
{
    public $timestamps = true;

    protected $table = 'post_products';

    protected $fillable = ['post_id', 'product_id', 'quantity'];

    protected function casts(): array
    {
        return ['quantity' => 'integer'];
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
