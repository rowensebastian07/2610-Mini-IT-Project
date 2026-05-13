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
    // ✅ Helper method to verify if the user is a Committee Member
    private function authorizeCommittee(Club $club)
    {
        $membership = $club->users()->where('user_id', Auth::id())->first();

        if (!$membership || $membership->pivot->role !== ClubRole::COMMITTEE->value) {
            abort(403, 'Unauthorized action. Only committee members can manage events.');
        }
    }

    public function index()
    {
        $events = Event::all();
        return view('calendar.index', compact('events'));
    }

    public function show(Club $club, Event $event)
    {
        return view('events.show', compact('club', 'event'));
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
        'title'       => 'required|string|max:255',
        'date'        => 'required|date',
        'time'        => 'required',
        'description' => 'nullable|string|max:255',
        'location'    => 'nullable|string|max:255',
    ]);

    $event = $club->events()->create($validated);

    // ✅ Notify ALL members, including sender
    foreach ($club->users as $member) {
        $member->notify(new ClubNotification(
            $club,
            "New Event Scheduled: {$event->title} on {$event->date} at {$event->time}"
        ));
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
            'title'       => 'required|string|max:255',
            'date'        => 'required|date',
            'time'        => 'required',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'is_passed'   => 'nullable|boolean',
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

    // ✅ Multiple file upload handler
    public function uploadFiles(Request $request, $eventId)
{
    $event = Event::findOrFail($eventId);

    if ($request->hasFile('event_files')) {
        $paths = [];

        foreach ($request->file('event_files') as $file) {
            // ✅ store inside a folder named by event ID
            $paths[] = $file->store("event_uploads/{$event->id}", 'public');
        }

        $existing = $event->uploads ? json_decode($event->uploads, true) : [];
        $event->uploads = json_encode(array_merge($existing, $paths));
        $event->save();
    }

    return back()->with('success', 'Files uploaded successfully!');
}

public function viewUploads(Event $event)
{
    $files = $event->uploads ? json_decode($event->uploads, true) : [];
    return view('events.uploads', compact('event', 'files'));
}

public function deletePhoto(Request $request, Event $event)
{
    $filePath = $request->input('file_path');

    // Remove file from storage
    \Storage::disk('public')->delete($filePath);

    // Remove from JSON list
    $files = json_decode($event->uploads, true);
    $files = array_filter($files, fn($path) => $path !== $filePath);
    $event->uploads = json_encode(array_values($files));
    $event->save();

    return back()->with('success', 'Photo deleted successfully!');
}


}