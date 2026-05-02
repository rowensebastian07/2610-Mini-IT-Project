<?php

namespace App\Models;

use App\Enums\UserStatus;
use App\Enums\UserVerification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin', 'status', 'verification'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean', // Cast the 0/1 from DB to true/false
            'status'            => UserStatus::class,
            'verification'      => UserVerification::class,
        ];
    }

    // --------------------------
    // Relationships
    // --------------------------

    /**
     * Get the memberships for the user.
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class, 'user_id');
    }

    /**
     * Get the clubs the user belongs to.
     * Use withPivot to access 'role' (President, Member, etc.) from the membership table.
     */
    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'memberships')
            ->withPivot('role', 'status') // Added 'status' so you can see if their membership is pending
            ->withTimestamps();
    }

    /**
     * Clubs this user specifically created/owns.
     */
    public function ownedClubs(): HasMany
    {
        return $this->hasMany(Club::class, 'owner_id');
    }

    /**
     * Get all events for all clubs the user is in.
     */
    public function getEventsAttribute()
    {
        return $this->clubs()->with('events')->get()->pluck('events')->flatten();
    }
}
