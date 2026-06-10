<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\User;
use App\Enums\ClubRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClubTermController extends Controller
{
    public function storeNewTerm(Request $request, Club $club)
    {
        $validated = $request->validate([
            'new_term' => 'required|string|max:20', 
            'committee' => 'required|array',
            'committee.*.user_id' => 'required|exists:users,id',
            'committee.*.role' => 'required|in:' . implode(',', array_column(ClubRole::cases(), 'value')),
        ]);

        DB::transaction(function () use ($club, $validated) {
            // Step A: Archive all current active positions for this club
            DB::table('memberships')
                ->where('club_id', $club->id)
                ->where('status', 'active')
                ->update(['status' => 'archived']);

            // Step B: Attach the new committee members for the new term
            foreach ($validated['committee'] as $member) {
                $club->users()->attach($member['user_id'], [
                    'role' => $member['role'],
                    'term' => $validated['new_term'],
                    'status' => 'active'
                ]);
            }
        });

        return redirect()->back()->with('success', 'New term successfully initiated!');
    }

    public function assignMember(Request $request, Club $club)
    {

        $validated = $request->validate([
            'term' => 'required|string|max:20',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:' . implode(',', array_column(ClubRole::cases(), 'value')), // ✨ Fixed: Uses dynamic enum values instead of outdated hardcoded strings
        ]);

        DB::transaction(function () use ($club, $validated) {
            // 1. Archive this specific user's active statuses in OTHER terms first
            DB::table('memberships')
                ->where('club_id', $club->id)
                ->where('user_id', $validated['user_id'])
                ->where('status', 'active')
                ->where('term', '!=', $validated['term'])
                ->update(['status' => 'archived']);

            // 2. Safe custom unique combination insert or update for this specific term
            DB::table('memberships')->updateOrInsert(
                [
                    'club_id' => $club->id,
                    'user_id' => $validated['user_id'],
                    'term'    => $validated['term'],
                ],
                [
                    'role'       => $validated['role'],
                    'status'     => 'active',
                    'updated_at' => now(),
                ]
            );
        });

        return redirect()->back()->with('success', 'Member successfully assigned to the term!');
    }

    public function show(Club $club)
    {
        $allUsers = User::orderBy('name')->get();

        $activeCommittee = $club->users()
            ->wherePivot('status', 'active')
            ->get();

        return view('clubs.committee', compact('club', 'allUsers', 'activeCommittee'));
    }
}