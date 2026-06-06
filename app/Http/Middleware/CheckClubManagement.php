<?php

namespace App\Http\Middleware;

use Closure;
use App\Enums\ClubRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckClubManagement
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ensure the user is logged in
        if (!Auth::check()) {
            abort(401, 'Please log in first.');
        }

        // 2. Automatically grab the club object or club ID from the current route URL
        $club = $request->route('club');
        $clubId = is_object($club) ? $club->id : $club;

        // If there is no club context in the URL, check if updating a post resource
        if (!$clubId && $request->route('post')) {
            $postParam = $request->route('post');
            
            // If route model binding resolved it to an object, grab the foreign key
            if (is_object($postParam)) {
                $clubId = $postParam->club_id;
            } else {
                // If it's just an ID integer, query it from the DB
                $clubId = DB::table('posts')->where('id', $postParam)->value('club_id');
            }
        }

        // Fallback safety check
        if (!$clubId) {
            abort(404, 'Club context not found.');
        }

        // 3. Query memberships table 
        $membership = DB::table('memberships')
            ->where('club_id', $clubId)
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->first();

        // 4. Define permitted management roles using your cleaned Enums
        $managementRoles = [
            ClubRole::PRESIDENT->value,
            ClubRole::HICOM->value,
            ClubRole::SUBCOM->value,
        ];

        // 5. Block anyone who isn't an active management committee member
        if (!$membership || !in_array($membership->role, $managementRoles)) {
            abort(403, 'Unauthorized. Only club management committee members can access this area.');
        }

        // If authorized, let the request proceed to the controller!
        return $next($request);
    }
}