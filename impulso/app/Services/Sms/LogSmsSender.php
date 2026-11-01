<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

/** Implementación provisoria: en lugar de enviar el SMS lo deja registrado en el log hasta contar con un proveedor real. */
class LogSmsSender implements SmsSender
{
    /** Registra el mensaje en el log. */
    public function send(string $to, string $message): void
    {
        Log::info('SMS pendiente de proveedor', ['to' => $to, 'message' => $message]);
    }
}
