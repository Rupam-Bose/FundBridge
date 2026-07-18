<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VideoCallController extends Controller
{
    public function show(int $partnerId)
    {
        $partner  = User::findOrFail($partnerId);
        $myId     = Auth::id();

        // Deterministic room: both users land in the same room
        $roomName = 'fundbridge-' . min($myId, $partnerId) . '-' . max($myId, $partnerId);

        return view('video-call', compact('partner', 'roomName'));
    }
}
