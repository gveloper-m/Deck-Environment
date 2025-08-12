<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingController extends Controller
{
    // Create a meeting (logged-in user)
    public function store(Request $request)
    {
        $request->validate([
            'when' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $meeting = Meeting::create([
            'user_id' => Auth::id(),
            'when' => $request->when,
            'notes' => $request->notes,
        ]);

        return response()->json($meeting, 201);
    }

    // User: Get all their meetings
    public function myMeetings()
    {
        return response()->json(Meeting::where('user_id', Auth::id())->get());
    }

    // User: Past meetings
    public function myPastMeetings()
    {
        return response()->json(Meeting::where('user_id', Auth::id())
            ->where('when', '<', now())
            ->get());
    }

    // User: Future meetings
    public function myFutureMeetings()
    {
        return response()->json(Meeting::where('user_id', Auth::id())
            ->where('when', '>=', now())
            ->get());
    }

    // User: Update meeting
    public function update(Request $request, $id)
    {
        $meeting = Meeting::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'when' => 'nullable|date',
            'notes' => 'nullable|string',
            'happened' => 'nullable|boolean',
        ]);

        $meeting->update($request->only('when', 'notes', 'happened'));

        return response()->json($meeting);
    }

    // User: Delete meeting
    public function destroy($id)
    {
        $meeting = Meeting::where('user_id', Auth::id())->findOrFail($id);
        $meeting->delete();

        return response()->json(['message' => 'Meeting deleted']);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Functions
    |--------------------------------------------------------------------------
    */
    public function adminAllMeetings()
    {
        return response()->json(Meeting::all());
    }

    public function adminMeetingsByUser($userId)
    {
        return response()->json(Meeting::where('user_id', $userId)->get());
    }

    public function adminPastMeetings()
    {
        return response()->json(Meeting::where('when', '<', now())->get());
    }

    public function adminFutureMeetings()
    {
        return response()->json(Meeting::where('when', '>=', now())->get());
    }

    public function adminUpdateMeeting(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);

        $request->validate([
            'when' => 'nullable|date',
            'notes' => 'nullable|string',
            'happened' => 'nullable|boolean',
        ]);

        $meeting->update($request->only('when', 'notes', 'happened'));

        return response()->json($meeting);
    }

    public function adminDeleteMeeting($id)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->delete();

        return response()->json(['message' => 'Meeting deleted by admin']);
    }

}
