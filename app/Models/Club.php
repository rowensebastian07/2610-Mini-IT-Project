<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\ClubCategory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Club extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'category', // need this, otherwise can't mass-assign categories.
        'profile_picture',
        'owner_id'
    ];

    // app/Models/Club.php
    public function members()
    {
        // This assumes you have a pivot table named 'club_user'
        return $this->belongsToMany(User::class);
    }

    protected $casts = [
        'category' => ClubCategory::class,
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * The user who created/owns the club.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Direct access to membership records.
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class, 'club_id');
    }

    /**
     * The users that belong to the club.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'memberships', 'club_id', 'user_id')
                    ->withPivot('role', 'status', 'verification')
                    ->withTimestamps(); // Use this if your pivot table has created_at/updated_at
    }

    /**
     * Relationship: Events hosted by this club.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'club_id');
    }

    /**
     * Helper: Check if the club owner is email-verified.
     * Useful for UI logic (e.g., hiding unverified clubs).
     */
    public function hasVerifiedOwner(): bool
    {
        return $this->owner->hasVerifiedEmail();
    }

    /**
     * A club is active if it has an owner and hasn't been soft-deleted.
     */
    public function isActive(): bool
    {
        return !is_null($this->owner_id);
    }
}

