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
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

#[Fillable(['name', 'email', 'password', 'is_admin', 'status', 'verification', 'profile_picture'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'status'            => UserStatus::class,
            'verification'      => UserVerification::class,
            'followed_clubs'    => 'array', // ✅ automatic JSON casting
        ];
    }

    // --------------------------
    // Relationships
    // --------------------------

    /**
     * Memberships for clubs (pivot table).
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class, 'user_id');
    }

    /**
     * Clubs the user belongs to.
     */
    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'memberships')
            ->withPivot('role', 'status', 'term')
            ->withTimestamps();
    }

    /**
     * Clubs owned by the user.
     */
    public function ownedClubs(): HasMany
    {
        return $this->hasMany(Club::class, 'owner_id');
    }

    /**
     * All events across clubs the user is a member of.
     */
    public function getEventsAttribute()
    {
        return $this->clubs()->with('events')->get()->pluck('events')->flatten();
    }

   public function getProfilePictureAttribute($value)
{
    return $value ? asset($value) : asset('images/default_pp.png');
}

    public function sendEmailVerificationNotification()
    {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Verify Your Account')
                ->greeting('Hello!')
                ->line('Welcome aboard! Please click the button below to verify your email address and activate your account.')
                ->action('Verify Email Address', $url)
                ->line('If you did not create an account, no further action is required.')
                ->salutation('Regards, ' . config('app.name'));
        });

        $this->notify(new VerifyEmail);
    }
    
}
