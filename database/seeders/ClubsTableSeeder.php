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
                'email' => 'mmusic@mmu.edu.my',
                'registration_link' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'registration_open' => false,
                'category' => ClubCategory::ART->value,
                'profile_picture' => 'images/1.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'MMU Superheroes',
                'email' => 'superheroes@mmu.edu.my',
                'registration_link' => null,
                'registration_open' => false,
                'category' => ClubCategory::COMMUNITY->value,
                'profile_picture' => 'images/2.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Buddhist Society',
                'email' => 'buddhist_society@mmu.edu.my',
                'registration_link' => null,
                'registration_open' => false,
                'category' => ClubCategory::RELIGION->value,
                'profile_picture' => 'images/3.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'MMU Esports',
                'email' => 'mmuesports@mmu.edu.my',
                'registration_link' => null,
                'registration_open' => false,
                'category' => ClubCategory::ENTERTAINMENT->value,
                'profile_picture' => 'images/4.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Chinese Language Society',
                'email' => 'chinese_language@mmu.edu.my',
                'registration_link' => null,
                'registration_open' => false,
                'category' => ClubCategory::CULTURAL->value,
                'profile_picture' => 'images/5.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'IT Society',
                'email' => 'itsociety@mmu.edu.my',
                'registration_link' => null,
                'registration_open' => false,
                'category' => ClubCategory::TECH->value,
                'profile_picture' => 'images/6.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Badminton Club',
                'email' => 'badminton@mmu.edu.my',
                'registration_link' => null,
                'registration_open' => false,
                'category' => ClubCategory::RECREATIONAL->value,
                'profile_picture' => 'images/7.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'CyberFitness Club',
                'email' => 'cyberfitness@mmu.edu.my',
                'registration_link' => null,
                'registration_open' => false,
                'category' => ClubCategory::RECREATIONAL->value,
                'profile_picture' => 'images/8.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'TechGirls MMU',
                'email' => 'techgirls@mmu.edu.my',
                'registration_link' => null,
                'registration_open' => false,
                'category' => ClubCategory::TECH->value,
                'profile_picture' => 'images/9.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Rentak Dance Club',
                'email' => 'rentak_dance@mmu.edu.my',
                'registration_link' => null,
                'registration_open' => false,
                'category' => ClubCategory::ART->value,
                'profile_picture' => 'images/10.jpg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Chess Club',
                'email' => 'chessclub@mmu.edu.my',
                'registration_link' => null,
                'registration_open' => false,
                'category' => ClubCategory::ENTERTAINMENT->value,
                'profile_picture' => 'images/11.jpeg',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'University Peer Group',
                'email' => 'peer_group@mmu.edu.my',
                'registration_link' => null,
                'registration_open' => false,
                'category' => ClubCategory::COMMUNITY->value,
                'profile_picture' => 'images/12.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Table Tennis Club',
                'email' => 'tabletennis@mmu.edu.my',
                'registration_link' => null,
                'registration_open' => false,
                'category' => ClubCategory::RECREATIONAL->value,
                'profile_picture' => 'images/13.png',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}