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
        'email',
        'instagram',
        'website',
        'banner_image',
        'registration_link',
        'registration_open',
        'theme',
        'is_Verified',
        'owner_id',
    ];

    protected $casts = [
        'registration_open' => 'boolean',
        'category' => ClubCategory::class,
        'faq' => 'array',
    ];

    // app/Models/Club.php
    public function members()
    {
        // This assumes you have a pivot table named 'club_user'
        return $this->belongsToMany(User::class);
    }

    public function followersCount()
    {
        return \App\Models\User::whereJsonContains('followed_clubs', $this->id)->count();
    }

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
                ->withTimestamps();
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
    
    public function messages()
{
    return $this->hasMany(Message::class);
}

public function treasurer()
{
    return $this->hasOne(Treasurer::class);
}

public function products()
{
    return $this->hasMany(Product::class);
}

    public function faqs()
    {
        return $this->hasMany(Faq::class); 
    }
}
