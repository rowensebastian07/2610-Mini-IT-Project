<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Club; 
use App\Models\Post; 
use App\Enums\UserStatus;
use App\Enums\UserVerification;
use App\Enums\ClubRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =======================================================
        // 1. RUN EXTERNAL SEEDERS FIRST (Ensures Club ID 1 exists)
        // =======================================================
        $this->call(ClubsTableSeeder::class);
        
        $club = Club::where('name', 'MMusic Club')->first() ?? Club::find(1);

        if (!$club) {
        // Ultimate safety shield: Only runs if the database is bone dry
        $club = Club::create([
            'name' => 'MMusic Club',
            'category' => 'Arts & Culture',
            'theme' => 'default',
        ]);
    }

        // =======================================================
        // 2. CREATE SYSTEM AUTHENTICATION USERS
        // =======================================================

        $presidentUser = User::UpdateOrCreate([
            'name' => 'President of Music Club',
            'email' => 'admin@club.com', 
            'password' => Hash::make('password'),
            'email_verified_at' => "2026-05-28 12:00:00"
        ]);

        // Verified System Admin
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

        // Unverified Student
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

        // =======================================================
        // 3. CREATE ADDITIONAL CLUB COMMITTEE & MEMBERS
        // =======================================================

        $committeeLead = User::create([
            'name' => 'Committee Lead',
            'email' => 'committee@club.com', 
            'password' => Hash::make('password'),
            'is_admin' => false,
            'email_verified_at' => "2026-05-28 12:00:00",
            'status' => UserStatus::ACTIVE->value,
            'verification' => UserVerification::VERIFIED->value,
        ]);

        $subCom = User::create([
            'name' => 'sub Committee',
            'email' => 'subcom@club.com', 
            'password' => Hash::make('password'),
            'is_admin' => false,
            'email_verified_at' => "2026-05-28 12:00:00",
            'status' => UserStatus::ACTIVE->value,
            'verification' => UserVerification::VERIFIED->value,
        ]);

        $regularMember = User::create([
            'name' => 'Regular Student',
            'email' => 'student@club.com',
            'password' => Hash::make('password'),
        ]);

        // =======================================================
        // 4. ATTACH USERS TO CLUB (Only Once to Prevent SQL Duplicate Errors)
        // =======================================================

        // Attach testing President
        $club->users()->attach($presidentUser->id, [
            'role' => ClubRole::PRESIDENT->value,
            'term' => '2025/2026',
            'status' => 'active',
        ]);

        // Attach High Committee member
        $club->users()->attach($committeeLead->id, [
            'role' => ClubRole::HICOM->value, 
            'term' => '2025/2026',  
            'status' => 'active'
        ]);

        $club->users()->attach($subCom->id, [
            'role' => ClubRole::SUBCOM->value, 
            'term' => '2025/2026',  
            'status' => 'active'
        ]);

        // Attach Regular Member
        $club->users()->attach($regularMember->id, [
            'role' => ClubRole::MEMBER->value,
            'term' => '2025/2026',
            'status' => 'active'
        ]);

        // =======================================================
        // 5. POST GENERATION
        // =======================================================
        
        Post::create([
            'club_id' => $club->id,
            'user_id' => $committeeLead->id,
            'title' => 'Welcome to MMusic Club!',
            'content' => 'This is our first official post.',
        ]);
    }
}