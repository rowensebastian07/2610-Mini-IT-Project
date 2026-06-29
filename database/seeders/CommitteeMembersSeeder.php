<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Club;

class CommitteeMembersSeeder extends Seeder
{
    public function run()
    {
        $clubs = Club::all();

        foreach ($clubs as $club) {
            $exists = DB::table('committee_members')
                ->where('club_id', $club->id)
                ->where('role', 'President')
                ->exists();

            if (!$exists) {
                DB::table('committee_members')->insert([
                    'club_id'        => $club->id,
                    'name'           => $club->owner->name ?? 'N/A',
                    'role'           => 'President',
                    'description'    => 'N/A',
                    'profile_picture'=> 'images/mmu.png',
                    'status'         => 'accepted',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }
    }
}
