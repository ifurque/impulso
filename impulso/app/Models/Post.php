<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\MediaUrl;

/**
 * Publicación del emprendimiento (novedad u oferta) con fotos, precio/descuento y vigencia.
 * Puede vincular productos (post_products) para descontar stock.
 */
class Post extends Model
{
    protected $fillable = ['business_id', 'created_by', 'title', 'body', 'photo', 'photos', 'type', 'price', 'discount_price', 'starts_at', 'ends_at', 'is_published', 'share_on_social'];

    protected function casts(): array
    {
        return ['photos' => 'array', 'price' => 'decimal:2', 'discount_price' => 'decimal:2', 'starts_at' => 'date', 'ends_at' => 'date', 'is_published' => 'boolean', 'share_on_social' => 'boolean'];
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return MediaUrl::from($this->photo);
    }

    public function getPhotoUrlsAttribute(): array
    {
        $photos = $this->photos ?: ($this->photo ? [$this->photo] : []);

        return collect($photos)->map(fn ($photo) => MediaUrl::from($photo))->filter()->values()->all();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'post_products')->withPivot('quantity')->withTimestamps();
    }

    public function productItems()
    {
        return $this->hasMany(PostProduct::class, 'post_id', 'id');
    }

    /** Porcentaje de descuento entre price y discount_price, o null si no aplica. */
    public function discountPercentage(): ?int
    {
        if (! $this->price || ! $this->discount_price || (float) $this->price <= 0) {
            return null;
        }

        return (int) round((1 - ((float) $this->discount_price / (float) $this->price)) * 100);
    }
}