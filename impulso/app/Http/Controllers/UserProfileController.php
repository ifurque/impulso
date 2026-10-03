<?php

namespace App\Http\Controllers;

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
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'avatar' => ['nullable', 'image', 'max:5120'],
            'current_password' => ['required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        unset($data['current_password']);
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            $oldAvatar = $user->avatar;
            $data['avatar'] = $request->file('avatar')->store('users/avatars', 'public');
        } else {
            unset($data['avatar']);
        }

        $emailChanged = $user->email !== $data['email'];
        $user->update($data);
        if ($emailChanged) {
            $user->forceFill(['email_verified_at' => null])->save();
        }
        if (!empty($oldAvatar)) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return redirect()->route('user.profile')->with('success', 'Tu perfil fue actualizado.');
    }
}