<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The relations to eager load on every query.
     *
     * @var array<string>
     */
    protected $with = ['userProfile'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
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
            'password' => 'hashed',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            $user->userProfile()?->delete();
            $user->userRoles()->delete();
        });
    }

    /**
     * Get the user profile associated with the user.
     */
    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the user roles associated with the user.
     */
    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    /**
     * Get the reported projects associated with the user.
     */
    public function reportedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'reporter_user_id');
    }

    /**
     * Get the assigned projects associated with the user.
     */
    public function assignedProjects(): HasMany
    {
        return $this->hasMany(Project::class, 'assigned_user_id');
    }

    /**
     * Get the project updates associated with the user.
     */
    public function projectUpdates(): HasMany
    {
        return $this->hasMany(ProjectUpdate::class, 'updater_user_id');
    }

    /**
     * Check if the user has administrative privileges.
     */
    public function isAdmin(): bool
    {
        return $this->userRoles->contains('role', \App\Enums\Role::ADMIN);
    }
}
