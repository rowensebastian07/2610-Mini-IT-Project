<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Club;
use App\Notifications\ClubNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EventController extends Controller
{
    // ✨ Cleaned: The internal authorizeCommittee() method is completely removed 
    // because the 'club.management' middleware handles it before reaching here.

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
        return view('events.create', compact('club'));
    }

    public function store(Request $request, Club $club)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'required|date',
            'time'        => 'required',
            'description' => 'nullable|string|max:255',
            'location'    => 'nullable|string|max:255',
        ]);

        $event = $club->events()->create($validated);

        // Notify ALL members
        foreach ($club->users as $member) {
            $member->notify(new ClubNotification(
                $club,
                "New Event Scheduled: {$event->title} on {$event->date} at {$event->time}",
                'event' 
            ));
        }

        return redirect()->route('clubs.show', $club->id)->with('success', 'Event created and members notified!');
    }

    public function edit(Club $club, Event $event)
    {
        return view('events.edit', compact('club', 'event'));
    }

    public function update(Request $request, Club $club, Event $event)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'required|date',
            'time'        => 'required',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'is_passed'   => 'nullable|boolean',
        ]);

        $event->update($validated);

        return redirect()->route('clubs.show', $club->id)->with('success', 'Event updated successfully!');
    }

    public function destroy(Club $club, Event $event)
    {
        $event->delete();

        return redirect()->route('clubs.show', $club->id)->with('success', 'Event deleted successfully!');
    }

    public function pastEvents(Club $club)
    {
        $today = Carbon::today();

        $pastEvents = $club->events()
            ->whereDate('date', '<', $today)
            ->orderBy('date', 'desc')
            ->get();

        return view('events.past', compact('club', 'pastEvents'));
    }

    public function uploadFiles(Request $request, Club $club, Event $event)
    {
        $request->validate([
            'event_files.*' => 'required|file|max:10240',
        ]);

        if ($request->hasFile('event_files')) {
            $paths = [];

            foreach ($request->file('event_files') as $file) {
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

    //Injected Club $club model context so $event->club handles cleanly without crashing
    public function deletePhoto(Request $request, Club $club, Event $event)
    {
        $filePath = $request->input('file_path');

        // Remove file from storage
        Storage::disk('public')->delete($filePath);

        // Remove from JSON list
        $files = json_decode($event->uploads, true);
        $files = array_filter($files, fn($path) => $path !== $filePath);
        $event->uploads = json_encode(array_values($files));
        $event->save();

        return back()->with('success', 'Photo deleted successfully!');
    }
}