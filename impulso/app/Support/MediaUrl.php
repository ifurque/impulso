<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Utilidades para armar URLs de archivos subidos. */
class MediaUrl
{
    /** Devuelve la URL pública de un archivo; deja intactas las URLs absolutas y data URIs y limpia prefijos viejos (storage/, uploads/, public/). */
    public static function from(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', 'data:'])) {
            return $path;
        }

        $path = Str::after(ltrim($path, '/'), 'storage/');
        $path = Str::after($path, 'uploads/');
        $path = Str::after($path, 'public/');

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->url($path);
    }
}