<?php

namespace App\Models;

use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'position',
        'phone',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }


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
        ];
    }

    /**
     * Get user's initials
     */
    public function initials(): string
    {
        return implode('', array_map(function ($word) {
            return strtoupper($word[0]);
        }, explode(' ', $this->name)));
    }

    /**
     * Check if user is an administrator
     */
    public function isAdministrator(): bool
    {
        return $this->role === 'administrator';
    }

    /**
     * Check if user is DOA staff
     */
    public function isDoaStaff(): bool
    {
        return $this->role === 'doa_staff';
    }

    /**
     * Check if user is a farmer
     */
    public function isFarmer(): bool
    {
        return $this->role === 'farmer';
    }
    public function farms()
    {
        return $this->belongsToMany(Farm::class)
            ->withPivot(['role', 'is_visible', 'role'])
            ->wherePivot('is_visible', true);
    }
}
