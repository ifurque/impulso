<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Reseña (puntaje y comentario) de un usuario sobre un emprendimiento; una por usuario. */
class Review extends Model
{
    protected $fillable = ['business_id', 'user_id', 'reviewer_name', 'rating', 'body', 'is_visible'];
    protected function casts(): array { return ['rating' => 'integer', 'is_visible' => 'boolean']; }
    public function user() { return $this->belongsTo(User::class); }
}