<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Club;
use App\Notifications\ClubNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function create(Club $club)
    {
        return view('events.create', compact('club'));
    }

    public function store(Request $request, Club $club)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date'  => 'required|date',
            'time'  => 'required',
            'description' => 'nullable|string',
        ]);

        // Create the event
        $event = $club->events()->create($validated);

        // Notify all members except the creator
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

    public function show(Club $club, Event $event)
{
    return view('events.show', compact('club', 'event'));
}

    public function edit(Club $club, Event $event)
    {
        return view('events.edit', compact('club', 'event'));
    }

    
    public function update(Request $request, Club $club, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date'  => 'required|date',
            'time'  => 'required',
            'description' => 'nullable|string',
        ]);

        $event->update($validated);

        return redirect()->route('clubs.show', $club->id)
                         ->with('success', 'Event updated successfully!');
    }

    public function destroy(Club $club, Event $event)
    {
        $event->delete();

        return redirect()->route('clubs.show', $club->id)
                         ->with('success', 'Event deleted successfully!');
    }
}
