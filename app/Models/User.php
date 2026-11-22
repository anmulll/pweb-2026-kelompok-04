<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'default_shipping_address',
        'role',
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
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function isBuyer(): bool
    {
        return $this->role === UserRole::Buyer;
    }

    public function isSeller(): bool
    {
        return $this->role === UserRole::Seller;
    }

    public function hasRole(UserRole|string $role): bool
    {
        $roleValue = is_string($role) ? strtolower($role) : $role->value;
        $userRoleValue = $this->role instanceof UserRole ? $this->role->value : (string) $this->role;

        return $userRoleValue === $roleValue;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->default_shipping_address;
    }

    public function setAddressAttribute(?string $value): void
    {
        $this->attributes['default_shipping_address'] = $value;
    }

    public function getPhoneNumberAttribute(): ?string
    {
        return $this->phone;
    }

    public function setPhoneNumberAttribute(?string $value): void
    {
        $this->attributes['phone'] = $value;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(Promotion::class, 'created_by');
    }
}
