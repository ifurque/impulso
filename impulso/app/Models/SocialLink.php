<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Enlace a una red social del emprendimiento, mostrado en su página pública. */
class SocialLink extends Model
{
    protected $fillable = ['business_id', 'platform', 'url', 'is_visible'];
    protected function casts(): array { return ['is_visible' => 'boolean']; }
}