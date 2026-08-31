<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = ['business_id', 'client_id', 'subject', 'message', 'response', 'status', 'answered_at'];

    protected function casts(): array
    {
        return [
            'answered_at' => 'datetime',
        ];
    }

    public function business() { return $this->belongsTo(Business::class); }

    public function client() { return $this->belongsTo(User::class, 'client_id'); }
}