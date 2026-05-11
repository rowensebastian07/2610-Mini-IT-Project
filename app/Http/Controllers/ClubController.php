<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Post;
use App\Enums\ClubRole;
use App\Notifications\ClubNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ClubController extends Controller
{
    // Homepage: list all clubs and posts
    public function index()
    {
        $clubs = Club::with('posts')->get();
        $posts = Post::with('club')->latest()->get();

        return view('navigation', compact('clubs', 'posts'));
    }

    // Navigation/list view
    public function list()
    {
        $clubs = Club::all();
        return view('navigation', compact('clubs'));
    }

    // Show a single club page
    public function show(Club $club)
    {
        $club->load(['posts', 'events']);
        return view('clubs.show', compact('club'));
    }

    public function edit(Club $club)
    {
        return view('create-clubs.edit', compact('club'));
    }

    public function update(Request $request, Club $club)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'profile_picture' => 'nullable|image',
            'category' => 'required|string',
            'email' => 'nullable|string',
            'banner' => 'nullable|image',
            'registration_link' => 'nullable|url',
            'registration_open' => 'sometimes'
        ]);
        
        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('clubs', 'public');
        }

        $club->update($data);

        return redirect()->route('clubs.show', $club->id)
                        ->with('success', 'Club updated successfully!');
    }

    
    public function destroy($id)
    {
        $club = Club::findOrFail($id);
        $club->delete();

        return redirect()->route('clubs.index')
                         ->with('success', 'Club deleted successfully!');
    }

    /**
     * Show the form to send a notification.
     */
    public function showNotifyForm(Club $club)
    {
        $membership = $club->users()->where('user_id', Auth::id())->first();
        if (!$membership || $membership->pivot->role !== ClubRole::COMMITTEE->value) {
            abort(403, 'Unauthorized.');
        }

        return view('clubs.notify', compact('club'));
    }

    /**
     * Process and send the notification.
     */
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

    // Temporary store function as we figure out how to reroute things

    public function store(Request $request,  \App\Models\Club $clubs)
    {
        $validated = $request->validate([
        'name'   => 'required|string|max:255',
        'category' => 'required',
        'profile_picture'   => 'nullable|image|max:2048',
        'description' => 'nullable|string',
        'category' => 'required|string',
        'email' => 'nullable|string',
        'banner' => 'nullable|image',
        'registration_link' => 'nullable|url',
        'registration_open' => 'sometimes'
        ]);


        if ($request->hasFile('profile_picture')) {
        $validated['profile_picture'] = $request->file('profile_picture')->store('clubs', 'public');
         }
        else{
            $profile_picture = "images/1.png";
        }

        $validated['owner_id'] = Auth::id();

        Club::create($validated);

        return redirect()->route('navigation')
                        ->with('success', 'Club created successfully!');
    }

   public function search(Request $request) {
    $query = $request->input('query');
    $clubs = Club::where('name','like',"%{$query}%")
                 ->orWhere('description','like',"%{$query}%")
                 ->with('events')
                 ->paginate(10);
    return view('clubs.search', compact('clubs','query'));
}   

    public function create(Club $club)
    {
        return view('create-clubs.create', compact('club'));
    }
        
    public function committee(Club $club)
    { 
        $committee = DB::table('committee_members')
                    ->where('club_id', $club->id)
                    ->get();

        return view('clubs.committee', compact('club', 'committee'));
    }

    public function addCommitteeMember(Request $request, Club $club)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'description' => 'nullable|string',
            'profile_picture' => 'nullable|image'
        ]);

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('committee', 'public');
        }

        $data['club_id'] = $club->id;

        \DB::table('committee_members')->insert($data);

        return redirect()->route('clubs.committee', $club->id);
    }

    public function removeCommitteeMember(Club $club, $id)
    {
        \DB::table('committee_members')->where('id', $id)->delete();
        return redirect()->route('clubs.committee', $club->id);
    }
}