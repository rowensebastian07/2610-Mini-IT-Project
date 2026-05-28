<?php

namespace Database\Seeders;

use App\Models\User;
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
                'email_verified_at' => now(),
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
                'email_verified_at' => null,
                'status' => UserStatus::ACTIVE->value,
                'verification' => UserVerification::UNVERIFIED->value,
            ]
        );

        // 3. Create Committee and Member users
        $committee = User::create([
            'name' => 'Committee Lead',
            'email' => 'admin@club.com',
            'password' => Hash::make('password'),
            'name' => 'Committee Lead',
            'is_admin' => true,
            'email_verified_at' => "2026-05-28 12:00:00",
            'status' => UserStatus::ACTIVE->value,
            'verification' => UserVerification::VERIFIED->value,
        ]);

        $member = User::create([
            'name' => 'Regular Student',
            'email' => 'student@club.com',
            'password' => Hash::make('password'),
        ]);

        // 4. Seed clubs via ClubsTableSeeder
        $this->call(ClubsTableSeeder::class);

        // 5. Attach committee/member roles to one of the seeded clubs
        $club = \App\Models\Club::first(); // pick the first seeded club
        $club->users()->attach($committee->id, ['role' => ClubRole::COMMITTEE->value]);
        $club->users()->attach($member->id, ['role' => ClubRole::MEMBER->value]);

        // 6. Create a Post for that club
        \App\Models\Post::create([
            'club_id' => $club->id,
            'user_id' => $committee->id,
            'title' => 'Welcome to the Club!',
            'content' => 'This is our first official post.',
        ]);
    }
}
