<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Enums\ClubCategory;
use Carbon\Carbon;

class ClubsTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        Club::insert([
            [
                'name' => 'MMUsic Club',
                'category' => ClubCategory::ART->value,
                'profile_picture' => 'images/1.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'MMU Superheroes',
                'category' => ClubCategory::COMMUNITY->value,
                'profile_picture' => 'images/2.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Buddhist Society',
                'category' => ClubCategory::RELIGION->value,
                'profile_picture' => 'images/3.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'MMU Esports',
                'category' => ClubCategory::ENTERTAINMENT->value,
                'profile_picture' => 'images/4.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Chinese Language Society',
                'category' => ClubCategory::CULTURAL->value,
                'profile_picture' => 'images/5.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'IT Society',
                'category' => ClubCategory::TECH->value,
                'profile_picture' => 'images/6.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Badminton Club',
                'category' => ClubCategory::RECREATIONAL->value,
                'profile_picture' => 'images/7.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'CyberFitness Club',
                'category' => ClubCategory::RECREATIONAL->value,
                'profile_picture' => 'images/8.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'TechGirls MMU',
                'category' => ClubCategory::TECH->value,
                'profile_picture' => 'images/9.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Rentak Dance Club',
                'category' => ClubCategory::ART->value,
                'profile_picture' => 'images/10.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Chess Club',
                'category' => ClubCategory::ENTERTAINMENT->value,
                'profile_picture' => 'images/11.jpeg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'University Peer Group',
                'category' => ClubCategory::COMMUNITY->value,
                'profile_picture' => 'images/12.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Table Tennis Club',
                'category' => ClubCategory::RECREATIONAL->value,
                'profile_picture' => 'images/13.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}

