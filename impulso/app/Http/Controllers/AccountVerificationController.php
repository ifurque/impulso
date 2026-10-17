<?php

namespace App\Http\Controllers;

use App\Services\Sms\SmsSender;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Throwable;

class AccountVerificationController extends Controller
{
    public function sendEmail(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return back()->with('success', 'Tu correo ya está verificado.');
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['verification' => 'No pudimos enviar el correo de verificación. Inténtalo más tarde.']);
        }

        return back()->with('success', 'Te enviamos un correo con el enlace para verificar tu dirección.');
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('user.profile')->with('success', 'Tu correo quedó verificado.');
    }

    public function sendPhone(Request $request, SmsSender $sms)
    {
        $user = $request->user();

        if (blank($user->phone)) {
            return back()->withErrors(['verification' => 'Guarda un teléfono antes de verificarlo.']);
        }

        if ($user->hasVerifiedPhone()) {
            return back()->with('success', 'Tu teléfono ya está verificado.');
        }

        $link = URL::temporarySignedRoute('phone.verification.verify', now()->addMinutes(60), [
            'id' => $user->getKey(),
            'hash' => sha1($user->phone),
        ]);

        try {
            $sms->send($user->phone, "Impulso: confirma tu teléfono ingresando a {$link}");
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['verification' => 'No pudimos enviar el mensaje de verificación. Inténtalo más tarde.']);
        }

        return back()->with('success', 'Te enviamos un mensaje con el enlace para verificar tu teléfono.');
    }

    public function verifyPhone(Request $request, string $id, string $hash)
    {
        $user = $request->user();

        abort_unless(
            (string) $user->getKey() === $id
                && filled($user->phone)
                && hash_equals(sha1($user->phone), $hash),
            403
        );

        if (! $user->hasVerifiedPhone()) {
            $user->forceFill(['phone_verified_at' => now()])->save();
        }

        return redirect()->route('user.profile')->with('success', 'Tu teléfono quedó verificado.');
    }
}
