<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Turno reservado en un emprendimiento. Puede ser de un usuario registrado (client)
 * o de un invitado (guest_name/guest_email); los tokens permiten confirmar y gestionar
 * (cancelar/reprogramar) el turno desde un enlace enviado por mail.
 */
class Appointment extends Model
{
    protected $fillable = ['business_id', 'client_id', 'product_id', 'guest_name', 'guest_email', 'appointment_date', 'start_time', 'end_time', 'status', 'confirmation_token', 'management_token', 'confirmed_at', 'notes'];
    protected function casts(): array { return ['appointment_date' => 'date', 'confirmed_at' => 'datetime']; }
    public function client() { return $this->belongsTo(User::class, 'client_id')->withDefault(['name' => 'Cliente invitado']); }
    public function product() { return $this->belongsTo(Product::class); }
    public function business() { return $this->belongsTo(Business::class); }
}