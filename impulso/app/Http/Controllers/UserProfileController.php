<?php

namespace App\Http\Controllers;

use App\Support\ArgentinaProvinces;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('settings.profile', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $document = preg_replace('/[\s.\-]/', '', (string) $request->input('document_number'));
        $request->merge(['document_number' => filled($document) ? $document : null]);

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'document_number' => ['nullable', 'regex:/^\d{7,9}$/', Rule::unique('users', 'document_number')->ignore($user->id)],
            'email' => ['required', 'email', 'max:180', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:180'],
            'postal_code' => ['nullable', 'string', 'max:10', 'regex:/^[A-Za-z0-9 \-]{3,10}$/'],
            'city' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', Rule::in(ArgentinaProvinces::all())],
            'avatar' => ['nullable', 'image', 'max:5120'],
        ], [
            'document_number.regex' => 'El DNI debe tener entre 7 y 9 números.',
            'document_number.unique' => 'Ese DNI ya está registrado en otra cuenta.',
            'postal_code.regex' => 'El código postal no es válido.',
        ]);

        $data['name'] = trim($data['first_name'].' '.$data['last_name']);

        if ($request->hasFile('avatar')) {
            $oldAvatar = $user->avatar;
            $data['avatar'] = $request->file('avatar')->store('users/avatars', 'public');
        } else {
            unset($data['avatar']);
        }

        $emailChanged = $user->email !== $data['email'];
        $phoneChanged = ($user->phone ?? '') !== ($data['phone'] ?? '');
        $user->update($data);
        if ($emailChanged) {
            $user->forceFill(['email_verified_at' => null])->save();
        }
        if ($phoneChanged) {
            $user->forceFill(['phone_verified_at' => null])->save();
        }
        if (!empty($oldAvatar)) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return redirect()->route('user.profile')->with('success', 'Tu perfil fue actualizado.');
    }
}