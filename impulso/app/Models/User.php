<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Support\MediaUrl;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_members')
            ->withPivot(['role', 'permissions', 'joined_at'])->withTimestamps();
    }

    public function ownedBusinesses()
    {
        return $this->hasMany(Business::class, 'owner_id');
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class, 'client_id');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'document_number',
        'email',
        'phone',
        'address',
        'postal_code',
        'city',
        'province',
        'avatar',
        'is_entrepreneur',
        'role',
        'ui_preferences',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'is_entrepreneur' => 'boolean',
            'ui_preferences' => 'array',
            'password' => 'hashed',
        ];
    }

    public function hasVerifiedPhone(): bool
    {
        return filled($this->phone) && $this->phone_verified_at !== null;
    }

    /** @return array{0: string, 1: string} */
    public static function splitName(?string $name): array
    {
        $parts = preg_split('/\s+/', trim((string) $name), 2) ?: [];

        return [$parts[0] ?? '', $parts[1] ?? ''];
    }

    /** @return array{0: string, 1: string} */
    public function nameParts(): array
    {
        if (filled($this->first_name) || filled($this->last_name)) {
            return [(string) $this->first_name, (string) $this->last_name];
        }

        return self::splitName($this->name);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return MediaUrl::from($this->avatar);
    }
}
