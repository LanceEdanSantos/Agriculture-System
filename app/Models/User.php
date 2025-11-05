<?php

namespace App\Models;

use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fname',
        'lname',
        'mname',
        'suffix',
        'email',
        'password',
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
    public function getFilamentName(): string
    {
        return trim("{$this->fname} {$this->lname}");
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'fname',
                'lname',
                'mname',
                'suffix',
                'email',
                'department',
                'position',
                'phone',
                'address',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }

    /**
     * Get user's initials
     */
    public function initials(): string
    {
        $name = trim("{$this->fname} {$this->lname}");
        return collect(explode(' ', $name))
            ->map(fn($part) => strtoupper(substr($part, 0, 1)))
            ->join('');
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
    public function farms(): BelongsToMany
    {
        return $this->belongsToMany(Farm::class, 'farm_user', 'user_id', 'farm_id')
            ->withPivot(['role', 'is_visible'])
            ->withTimestamps();
    }

    /**
     * Get only visible farms (for admin UI purposes)
     */
    public function visibleFarms(): BelongsToMany
    {
        return $this->belongsToMany(Farm::class)
            ->withPivot(['role', 'is_visible'])
            ->withTimestamps()
            ->wherePivot('is_visible', true);
    }
}
