<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Administración de la plataforma: gestión de emprendimientos y usuarios. Solo para superadmins.
 */
class AdminController extends Controller
{
    /** Listado paginado de todos los emprendimientos. */
    public function businesses(Request $request)
    {
        $this->authorizeAdmin($request);

        return view('admin.businesses', ['businesses' => Business::with('owner')->latest()->paginate(20)]);
    }

    /** Listado paginado de usuarios con la cantidad de emprendimientos que tiene cada uno. */
    public function users(Request $request)
    {
        $this->authorizeAdmin($request);

        return view('admin.users', ['users' => User::withCount('ownedBusinesses')->latest()->paginate(20)]);
    }

    /** Formulario de edición de un emprendimiento. */
    public function editBusiness(Request $request, Business $business)
    {
        $this->authorizeAdmin($request);

        return view('admin.business-edit', ['business' => $business, 'users' => User::orderBy('name')->get()]);
    }

    /** Actualiza un emprendimiento; si cambia el nombre regenera el slug y borra las fotos reemplazadas. */
    public function updateBusiness(Request $request, Business $business)
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'owner_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:180'],
            'is_public' => ['sometimes', 'boolean'],
            'profile_photo' => ['nullable', 'image', 'max:5120'],
            'cover_photo' => ['nullable', 'image', 'max:5120'],
        ]);
        $data['is_public'] = $request->boolean('is_public');
        if ($data['name'] !== $business->name) {
            $data['slug'] = Str::slug($data['name']).'-'.Str::lower(Str::random(5));
        }
        foreach (['profile_photo', 'cover_photo'] as $field) {
            if ($request->hasFile($field)) {
                $oldPaths[$field] = $business->{$field};
                $data[$field] = $request->file($field)->store('businesses', 'public');
            } else {
                unset($data[$field]);
            }
        }
        $business->update($data);
        Storage::disk('public')->delete(array_filter($oldPaths ?? []));

        return redirect()->route('admin.businesses')->with('success', 'Emprendimiento actualizado.');
    }

    /** Elimina un emprendimiento junto con sus archivos (fotos, fondos, productos y publicaciones). */
    public function deleteBusiness(Request $request, Business $business)
    {
        $this->authorizeAdmin($request);
        $business->load(['posts', 'products']);
        $paths = collect([
            $business->profile_photo,
            $business->cover_photo,
            $business->public_background_image,
            $business->public_pattern_image,
        ])->merge($business->products->pluck('photo'));
        foreach ($business->posts as $post) {
            $paths = $paths->merge($post->photos ?? [])->push($post->photo);
        }
        Storage::disk('public')->delete($paths->filter()->unique()->all());
        $business->delete();

        return back()->with('success', 'Emprendimiento eliminado.');
    }

    /** Activa o quita el plan +Impulso de un emprendimiento. */
    public function togglePlus(Request $request, Business $business)
    {
        $this->authorizeAdmin($request);
        $business->update(['is_plus' => !$business->is_plus]);

        return back()->with('success', $business->is_plus ? '+Impulso activado.' : '+Impulso quitado.');
    }

    /** Formulario de edición de un usuario. */
    public function editUser(Request $request, User $user)
    {
        $this->authorizeAdmin($request);

        return view('admin.user-edit', compact('user'));
    }

    /** Actualiza un usuario; si cambia el correo o el teléfono, invalida su verificación. */
    public function updateUser(Request $request, User $user)
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'avatar' => ['nullable', 'image', 'max:5120'],
            'is_entrepreneur' => ['sometimes', 'boolean'],
        ]);
        $data['is_entrepreneur'] = $request->boolean('is_entrepreneur');
        $oldAvatar = null;
        if ($request->hasFile('avatar')) {
            $oldAvatar = $user->avatar;
            $data['avatar'] = $request->file('avatar')->store('users/avatars', 'public');
        } else {
            unset($data['avatar']);
        }
        $emailChanged = $user->email !== $data['email'];
        $phoneChanged = ($user->phone ?? '') !== ($data['phone'] ?? '');
        if ($user->name !== $data['name']) {
            [$data['first_name'], $data['last_name']] = User::splitName($data['name']);
        }
        $user->update($data);
        if ($emailChanged) {
            $user->forceFill(['email_verified_at' => null])->save();
        }
        if ($phoneChanged) {
            $user->forceFill(['phone_verified_at' => null])->save();
        }
        if ($oldAvatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return redirect()->route('admin.users')->with('success', 'Usuario actualizado.');
    }

    /** Elimina un usuario y sus emprendimientos (con archivos); protege la cuenta propia y al último superadmin. */
    public function deleteUser(Request $request, User $user)
    {
        $this->authorizeAdmin($request);
        abort_if($request->user()->is($user), 422, 'No puedes eliminar tu propia cuenta desde este panel.');
        abort_if($user->role === 'superadmin' && User::where('role', 'superadmin')->count() <= 1, 422, 'Debe quedar al menos un administrador.');
        $user->ownedBusinesses()->with(['posts', 'products'])->get()->each(function (Business $business) {
            $business->loadMissing(['posts', 'products']);
            $paths = collect([$business->profile_photo, $business->cover_photo, $business->public_background_image, $business->public_pattern_image])
                ->merge($business->products->pluck('photo'));
            foreach ($business->posts as $post) {
                $paths = $paths->merge($post->photos ?? [])->push($post->photo);
            }
            Storage::disk('public')->delete($paths->filter()->unique()->all());
        });
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        $user->delete();

        return back()->with('success', 'Usuario y sus emprendimientos eliminados.');
    }

    /** Alterna el rol de un usuario entre superadmin y cliente, sin dejar la plataforma sin administradores. */
    public function toggleAdmin(Request $request, User $user)
    {
        $this->authorizeAdmin($request);
        abort_if($request->user()->is($user), 422, 'No puedes cambiar tu propio rol.');
        abort_if($user->role === 'superadmin' && User::where('role', 'superadmin')->count() <= 1, 422, 'Debe quedar al menos un administrador.');
        $user->update(['role' => $user->role === 'superadmin' ? 'client' : 'superadmin']);

        return back()->with('success', $user->role === 'superadmin' ? 'Administrador habilitado.' : 'Permisos de administrador quitados.');
    }

    /** Responde 403 si quien consulta no es superadmin. */
    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->role === 'superadmin', 403);
    }
}