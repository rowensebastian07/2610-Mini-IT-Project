<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Club;
use App\Enums\ClubRole;
use App\Notifications\ClubNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Helper method to verify if the user is a Committee Member.
     * This keeps the code DRY.
     */
    private function authorizeCommittee(Club $club)
    {
        $membership = $club->users()->where('user_id', Auth::id())->first();

        if (!$membership || $membership->pivot->role !== ClubRole::COMMITTEE->value) {
            abort(403, 'Unauthorized action. Only committee members can manage events.');
        }
    }

    public function create(Club $club)
    {
        $this->authorizeCommittee($club);
        return view('events.create', compact('club'));
    }

    public function store(Request $request, Club $club)
    {
        $this->authorizeCommittee($club);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date'  => 'required|date',
            'time'  => 'required',
        ]);

        // 1. Create the event using the relationship
        $event = $club->events()->create($validated);

        // 2. Notify members (Email + Database)
        foreach ($club->users as $member) {
            if ($member->id !== Auth::id()) {
                $member->notify(new ClubNotification(
                    $club, 
                    "New Event Scheduled: {$event->title} on {$event->date} at {$event->time}"
                ));
            }
        }

        return redirect()->route('clubs.show', $club->id)
                         ->with('success', 'Event created and members notified!');
    }

    public function edit(Club $club, Event $event)
    {
        $this->authorizeCommittee($club);
        return view('events.edit', compact('club', 'event'));
    }

    public function update(Request $request, Club $club, Event $event)
    {
        $this->authorizeCommittee($club);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date'  => 'required|date',
            'time'  => 'required',
        ]);

        $event->update($validated);

        return redirect()->route('clubs.show', $club->id)
                         ->with('success', 'Event updated successfully!');
    }

    public function destroy(Club $club, Event $event)
    {
        $this->authorizeCommittee($club);
        
        $event->delete();

        return redirect()->route('clubs.show', $club->id)
                         ->with('success', 'Event deleted successfully!');
    }
}