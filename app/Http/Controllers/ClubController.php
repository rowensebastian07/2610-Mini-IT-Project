<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Post;
use App\Enums\ClubRole;
use App\Notifications\ClubNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClubController extends Controller
{
    private function authorizeCommittee(Club $club)
    {
        $membership = $club->users()->where('user_id', Auth::id())->first();

        if (!$membership || $membership->pivot->role !== ClubRole::COMMITTEE->value) {
            abort(403, 'Unauthorized action. Only committee members can manage this club.');
        }
    }

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

        $today = Carbon::today();

        $upcomingEvents = $club->events->filter(fn($event) => Carbon::parse($event->date)->gte($today));
        $pastEvents     = $club->events->filter(fn($event) => Carbon::parse($event->date)->lt($today));

        return view('clubs.show', compact('club', 'upcomingEvents', 'pastEvents'));
    }

    // --------------------------
    // Edit club
    // --------------------------
    public function edit(Club $club)
    {
        $this->authorizeCommittee($club);
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

        $user = \App\Models\User::find($club->owner_id);

        
        $user->notify(new ClubNotification(
            $club,
            "Your club {$club->name} has been deleted. "
            ));
    


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
            'registration_open' => 'sometimes',
            'theme' => 'required|string'
        ]);

        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')->store('clubs', 'public');
        } else {
            $validated['profile_picture'] = "images/1.png";
        }

        $validated['owner_id'] = Auth::id();

        Club::create($validated);


        if ($user = auth()->user()) {
            $user->notify(new ClubNotification(
            $clubs,
            "Your club {$clubs->name} has been submitted for review. 
            The admins will review your club and determine if it's official. "
            ));
        }

        


        return redirect()->route('clubs.index')
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

        $president = DB::table('committee_members')
            ->where('club_id', $club->id)
            ->where('role', 'President')
            ->first();

        // ✅ Add remaining attempts calculation here
        $user = auth()->user();
        $searchCount = DB::table('search_logs')
            ->where('user_id', $user->id)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $remaining = max(0, 10 - $searchCount);

        return view('clubs.committee', compact('club', 'committee', 'president', 'remaining'));
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

    // Chatroom function 
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

    // --------------------------
    // Add committee member
    // --------------------------

    public function updateVerify(Request $request, Club $club)
    {
        $club->is_Verified = true;
        $club->save();

        $user = \App\Models\User::find($club->owner_id);

        
        $user->notify(new ClubNotification(
            $club,
            "Your club {$club->name} has been verified by admins and your page is available. 
            Congratulations!  "
            ));
    
        

        return redirect()->route('clubs.show', $club->id)
                         ->with('success', 'Club updated successfully and members notified!');
    }

    //Public Display Method
    public function faqView($id)
    {
        $club = Club::findOrFail($id);

        // Check if the current user is a committee member of THIS club
        $isCommittee = false;
        if (Auth::check()) {
            $membership = $club->users()->where('user_id', Auth::id())->first();
            $isCommittee = $membership && $membership->pivot->role === ClubRole::COMMITTEE->value;
        }
        
        // Pass $isCommittee safely down into the view template
        return view('clubs.faq', compact('club', 'isCommittee'));
    }

    public function updateFaq(Request $request, $id)
    {
        $club = Club::findOrFail($id);

        $this->authorizeCommittee($club);

        $request->validate([
            'faq' => 'nullable|array',
            'faq.*.question' => 'required|string',
            'faq.*.answer' => 'required|string',
        ]);

        $club->faq = $request->input('faq', []); 
        $club->save();

        // Redirect them straight back to the view-only page with the success confirmation!
        return redirect()->route('clubs.faq.view', $club->id)->with('success', 'FAQs updated successfully!');
    }
}