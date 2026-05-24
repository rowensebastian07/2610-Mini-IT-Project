<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Post;
use App\Enums\ClubRole;
use App\Notifications\ClubNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClubController extends Controller
{
    // --------------------------
    // Homepage: list all clubs and posts
    // --------------------------
    public function index()
    {
        $clubs = Club::with('posts')->get();
        $posts = Post::with('club')->latest()->get();
        return view('navigation', compact('clubs', 'posts'));
    }

    // --------------------------
    // Navigation/list view
    // --------------------------
    public function list()
    {
        $clubs = Club::all();
        return view('navigation', compact('clubs'));
    }

    // --------------------------
    // Show a single club page
    // --------------------------
    public function show(Club $club)
    {
        $user = Auth::user();
        $club->load(['posts', 'events']);
        return view('clubs.show', compact('club'));
    }

    // --------------------------
    // Edit club
    // --------------------------
    public function edit(Club $club)
    {
        return view('create-clubs.edit', compact('club'));
    }

    // --------------------------
    // Update club and notify members
    // --------------------------
    public function update(Request $request, Club $club)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'profile_picture'   => 'nullable|image',
            'category'          => 'required|string',
            'email'             => 'nullable|string',
            'banner_image'      => 'nullable|image',
            'registration_link' => 'nullable|url',
            'registration_open' => 'sometimes',
            'theme' => 'required|string'
        ]);

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('clubs', 'public');
        }

        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('banners', 'public');
        }

        $club->update($data);

        foreach ($club->users as $member) {
            $member->notify(new ClubNotification(
                $club,
                "{$club->name} has been updated — check out their latest features!",
                'club'
            ));
        }

        return redirect()->route('clubs.show', $club->id)
                         ->with('success', 'Club updated successfully and members notified!');
    }

    public function updateContact(Request $request, Club $club)
{
    $validated = $request->validate([
        'email' => 'nullable|email',
        'instagram' => 'nullable|string|max:255',
        'website' => 'nullable|url',
    ]);

    $club->update($validated);

    return redirect()->back()->with('success', 'Contact info updated successfully!');
}


    // --------------------------
    // Delete club
    // --------------------------
    public function destroy($id)
    {

        $club = Club::findOrFail($id);
        $club->delete();
        return redirect()->route('clubs.index')
                         ->with('success', 'Club deleted successfully!');
    }

    // --------------------------
    // Show notification form
    // --------------------------
    public function showNotifyForm(Club $club)
    {
        $membership = $club->users()->where('user_id', Auth::id())->first();
        if (!$membership || $membership->pivot->role !== ClubRole::COMMITTEE->value) {
            abort(403, 'Unauthorized.');
        }

        return view('clubs.notify', compact('club'));
    }

    // --------------------------
    // Send club update notification
    // --------------------------
    public function sendUpdate(Request $request, Club $club)
    {
        $membership = $club->users()->where('user_id', Auth::id())->first();
        if (!$membership || $membership->pivot->role !== ClubRole::COMMITTEE->value) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'message' => 'required|string|min:5',
        ]);

        $messageContent = $request->input('message');
        $members = $club->users;

        foreach ($members as $member) {
            if ($member->id !== Auth::id()) {
                $member->notify(new ClubNotification($club, $messageContent));
            }
        }

        return redirect()->route('clubs.show', $club->id)
                         ->with('status', 'Notification sent to ' . ($members->count() - 1) . ' members!');
    }

    // --------------------------
    // Create new club
    // --------------------------
    public function store(Request $request, Club $clubs)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'category' => 'required',
            'profile_picture'   => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'email' => 'nullable|string',
            'banner' => 'nullable|image',
            'registration_link' => 'nullable|url',
            'registration_open' => 'sometimes'
        ]);

        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')->store('clubs', 'public');
        } else {
            $validated['profile_picture'] = "images/1.png";
        }

        $validated['owner_id'] = Auth::id();

        Club::create($validated);

        return redirect()->route('navigation')
                         ->with('success', 'Club created successfully!');
    }

    // --------------------------
    // Search clubs
    // --------------------------
    public function search(Request $request)
    {
        $query = $request->input('query');
        $clubs = Club::where('name', 'like', "%{$query}%")
                     ->orWhere('description', 'like', "%{$query}%")
                     ->with('events')
                     ->paginate(10);
        return view('clubs.search', compact('clubs', 'query'));
    }

    // --------------------------
    // Create club form
    // --------------------------
    public function create(Club $club)
    {
        return view('create-clubs.create', compact('club'));
    }

    // --------------------------
    // Committee page
    // --------------------------
    public function committee(Club $club)
    {
        $committee = DB::table('committee_members')
            ->where('club_id', $club->id)
            ->get();

        return view('clubs.committee', compact('club', 'committee'));
    }

    // --------------------------
    // Add committee member
    // --------------------------
    public function addCommitteeMember(Request $request, Club $club)
{
    $data = $request->validate([
        'user_id'       => 'required|exists:users,id',
        'role'          => 'required|string|max:255',
        'profile_picture' => 'nullable|image',
    ]);

    if ($request->hasFile('profile_picture')) {
        $data['profile_picture'] = $request->file('profile_picture')->store('committee', 'public');
    } else {
        // ✅ Default image path
        $data['profile_picture'] = 'images/mmu.png';
    }

    $user = \App\Models\User::find($data['user_id']);

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

    return redirect()->route('clubs.committee', $club->id)
                     ->with('success', 'Invitation sent successfully!');
}

    // --------------------------
    // Respond to committee invite
    // --------------------------
    public function respondToInvite(Request $request, Club $club)
    {
        $action = $request->input('action'); // 'accept' or 'decline'

        DB::table('committee_members')
            ->where('club_id', $club->id)
            ->where('name', Auth::user()->name) // ✅ match by name
            ->update([
                'status'     => $action === 'accept' ? 'accepted' : 'declined',
                'updated_at' => now(),
            ]);

        return redirect()->route('clubs.committee', $club->id)
                         ->with('success', 'Your response has been recorded.');
    }

    public function updateCommitteeMember(Request $request, Club $club, $id)
{
    $data = $request->validate([
        'role' => 'required|string|max:255',
        'description' => 'nullable|string',
        'profile_picture' => 'nullable|image|max:2048',
    ]);

    $updateData = [
        'role' => $data['role'],
        'description' => $data['description'] ?? null,
        'updated_at' => now(),
    ];

    if ($request->hasFile('profile_picture')) {
        $updateData['profile_picture'] = $request->file('profile_picture')->store('committee', 'public');
    }

    DB::table('committee_members')
        ->where('id', $id)
        ->where('club_id', $club->id)
        ->update($updateData);

    return redirect()->route('clubs.committee', $club->id)
                     ->with('success', 'Profile updated successfully!');
}



    // --------------------------
    // Remove committee member
    // --------------------------
    public function removeCommitteeMember(Club $club, $id)
    {
        DB::table('committee_members')->where('id', $id)->delete();
        return redirect()->route('clubs.committee', $club->id);
    }

   // Chatroom fucntion 
public function chatroom(Club $club)
{
    $messages = $club->messages()
        ->with('user') // critical: ensures $message->user is populated
        ->orderBy('created_at')
        ->get();

    return view('clubs.chatroom', compact('club', 'messages'));
}

    // Update themes
    public function updateTheme(Request $request, Club $club)
    {
        $data = $request->validate([
            'theme' => 'required|string'
        ]);


        $club->update($data);

        return redirect()->route('clubs.show', $club->id)
                         ->with('success', 'Club updated successfully and members notified!');
    }



}