<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Club;
use App\Models\Membership;
use App\Models\Post;
use App\Modles\Events;
use App\Enums\ClubRole;
use App\Enums\UserStatus;
use App\Enums\UserVerification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a VERIFIED Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Verified Admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(), // This is what Laravel checks
                'status' => UserStatus::ACTIVE->value,
                'verification' => UserVerification::VERIFIED->value,
            ]
        );

        // 2. Create an UNVERIFIED User
        User::updateOrCreate(
            ['email' => 'student@example.com'],
            [
                'name' => 'New Student',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => null, // Empty means they aren't verified yet
                'status' => UserStatus::ACTIVE->value,
                'verification' => UserVerification::UNVERIFIED->value,
            ]
        );
        
        //Create the Committee User
        $committee = User::create([
            'name' => 'Committee Lead',
            'email' => 'admin@club.com',
            'password' => Hash::make('password'),
        ]);

        //Create the Regular Member
        $member = User::create([
            'name' => 'Regular Student',
            'email' => 'student@club.com',
            'password' => Hash::make('password'),
        ]);

        //Create the Club
        $club = Club::create([
            'name' => 'IT Society',
            'category' => 'Arts Clubs',
            'profile_picture' => 'images/1.png'
        ]);

        //use $committee and $member because they were defined above
        $club->users()->attach($committee->id, ['role' => ClubRole::COMMITTEE->value]);
        $club->users()->attach($member->id, ['role' => ClubRole::MEMBER->value]);

        //Create a Post
        Post::create([
            'club_id' => $club->id,
            'user_id' => $committee->id, // Uses the $committee variable from step 1
            'title' => 'Welcome to the Club!',
            'body' => 'This is our first official post.',
        ]);
    }
}
