<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, Club $club)
    {
        // Save the message in DB
        $message = $club->messages()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);

        // Broadcast the event to others
        broadcast(new MessageSent($message->load('user')))->toOthers();

        // Return JSON response
        return response()->json($message);
    }

  public function update(Request $request, Message $message)
{
    if ($message->user_id != auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // ✅ Block edits after 30 minutes
    if ($message->created_at->diffInMinutes(now()) > 30) {
        return response()->json(['error' => 'Edit time expired'], 403);
    }

    $message->body = $request->body;
    $message->save();

    return response()->json([
        'id' => $message->id,
        'body' => $message->body,
        'updated_at' => $message->updated_at,
        'user' => [
            'name' => $message->user->name,
            'profile_picture' => $message->user->profile_picture,
        ],
    ]);
}


    public function destroy(Message $message)
    {
        if ($message->user_id == auth()->id()) {
            $message->delete();
            return response()->noContent();
        }
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
