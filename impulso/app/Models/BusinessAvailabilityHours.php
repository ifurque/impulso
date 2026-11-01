<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Horario de atención de un día de la semana (apertura/cierre o cerrado) que define los turnos disponibles. */
class BusinessAvailabilityHours extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'day_of_week', 'opening_time', 'closing_time', 'is_closed'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
