<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Club;
use App\Models\User;
use App\Notifications\ClubNotification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CommitteeController extends Controller
{
    // --------------------------
    // Show committee members for a club
    // --------------------------
    public function index(Club $club)
    {
        // Exclude President from committee list to avoid duplicates
        $committee = DB::table('committee_members')
            ->where('club_id', $club->id)
            ->where('role', '!=', 'President')
            ->get();

        // Fetch President separately
        $president = DB::table('committee_members')
            ->where('club_id', $club->id)
            ->where('role', 'President')
            ->first();

        $user = Auth::user();
        $remaining = 0;

        if ($user) {
            $searchCount = DB::table('search_logs')
                ->where('user_id', $user->id)
                ->whereDate('created_at', Carbon::today())
                ->count();

            $remaining = max(0, 10 - $searchCount);
        }

        return view('clubs.committee', compact('club', 'committee', 'president', 'remaining'));
    }

    // --------------------------
    // Assign a new committee member
    // --------------------------
    public function store(Request $request, Club $club)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|string|max:255',
            'profile_picture' => 'nullable|image',
        ]);

        // Prevent duplicate President
        if ($data['role'] === 'President') {
            $exists = DB::table('committee_members')
                ->where('club_id', $club->id)
                ->where('role', 'President')
                ->exists();

            if ($exists) {
                return back()->with('error', 'President already assigned.');
            }
        }

        $data['profile_picture'] = $request->hasFile('profile_picture')
            ? $request->file('profile_picture')->store('committee', 'public')
            : 'images/mmu.png';

        $user = User::find($data['user_id']);

        DB::table('committee_members')->insert([
            'club_id'        => $club->id,
            'name'           => $user->name,
            'role'           => $data['role'],
            'profile_picture'=> $data['profile_picture'],
            'status'         => 'pending',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        $user->notify(new ClubNotification(
            $club,
            "You've been invited to join {$club->name} committee as {$data['role']}.",
            'committee'
        ));

        return redirect()->route('committee.index', ['club' => $club->id])
                         ->with('success', 'Invitation sent successfully!');
    }

    // --------------------------
    // Respond to an invitation (accept/decline)
    // --------------------------
    public function respond(Request $request, Club $club)
    {
        $action = $request->input('action'); // 'accept' or 'decline'

        DB::table('committee_members')
            ->where('club_id', $club->id)
            ->where('name', auth()->user()->name)
            ->update([
                'status'     => $action === 'accept' ? 'accepted' : 'declined',
                'updated_at' => now(),
            ]);

        return redirect()->route('committee.index', ['club' => $club->id])
                         ->with('success', 'Your response has been recorded.');
    }

    // --------------------------
    // Update committee member details
    // --------------------------
    public function update(Request $request, Club $club, $id)
    {
        $updateData = [
            'role'        => $request->input('role'),
            'description' => $request->input('description'),
            'updated_at'  => now(),
        ];

        if ($request->hasFile('profile_picture')) {
            $updateData['profile_picture'] = $request->file('profile_picture')->store('committee', 'public');
        }

        if ($id === 'president') {
            DB::table('committee_members')
                ->where('club_id', $club->id)
                ->where('role', 'President')
                ->update($updateData);
        } else {
            DB::table('committee_members')
                ->where('id', $id)
                ->where('club_id', $club->id)
                ->update($updateData);
        }

        return back()->with('success', 'Changes saved successfully!');
    }

    // --------------------------
    // Remove a committee member
    // --------------------------
    public function destroy(Club $club, $id)
    {
        if ($id === 'president') {
            DB::table('committee_members')
                ->where('club_id', $club->id)
                ->where('role', 'President')
                ->delete();
        } else {
            DB::table('committee_members')
                ->where('id', $id)
                ->where('club_id', $club->id)
                ->delete();
        }

        return redirect()->route('committee.index', ['club' => $club->id])
                         ->with('success', 'Member removed successfully!');
    }

    // --------------------------
    // Update committee background
    // --------------------------
    public function updateBackground(Request $request, Club $club)
    {
        $request->validate([
            'background' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $path = $request->file('background')->store('committee_backgrounds', 'public');

        $club->committee_background = $path;
        $club->save();

        return back()->with('success', 'Background updated successfully!');
    }

    // --------------------------
    // Update committee theme
    // --------------------------
    public function updateCommitteeTheme(Request $request, Club $club)
    {
        $request->validate([
            'theme' => 'required|string',
        ]);

        $club->committee_theme = $request->input('theme');
        $club->save();

        return back()->with('success', 'Committee theme updated successfully!');
    }
}
