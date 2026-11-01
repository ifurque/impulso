<?php

namespace App\Services\Sms;

/** Contrato para enviar SMS; permite cambiar de proveedor sin tocar los controladores. */
interface SmsSender
{
    /** Envía el mensaje de texto al número indicado. */
    public function send(string $to, string $message): void;
}
