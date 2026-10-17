<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\MediaUrl;

class Product extends Model
{
    protected $fillable = ['business_id', 'catalog_number', 'name', 'brand', 'model', 'description', 'photo', 'type', 'category', 'unit', 'quantity', 'price', 'duration', 'is_active'];
    protected function casts(): array { return ['quantity' => 'decimal:2', 'price' => 'decimal:2', 'is_active' => 'boolean']; }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_products')->withPivot('quantity')->withTimestamps();
    }

    public function postItems()
    {
        return $this->hasMany(PostProduct::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return MediaUrl::from($this->photo);
    }
}
