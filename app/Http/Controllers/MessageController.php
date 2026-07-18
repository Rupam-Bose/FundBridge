<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /** List all conversations for the current user */
    public function index()
    {
        $userId = Auth::id();

        // Get all unique conversation partners
        $sentTo = Message::where('sender_id', $userId)->distinct()->pluck('receiver_id');
        $receivedFrom = Message::where('receiver_id', $userId)->distinct()->pluck('sender_id');

        $partnerIds = $sentTo->merge($receivedFrom)->unique();

        $conversations = collect();
        foreach ($partnerIds as $pid) {
            $latest = Message::where(
                fn($q) =>
                $q->where('sender_id', $userId)->where('receiver_id', $pid)
            )->orWhere(
                    fn($q) =>
                    $q->where('sender_id', $pid)->where('receiver_id', $userId)
                )->latest()->first();

            if ($latest) {
                $partner = User::find($pid);
                $unread = Message::where('sender_id', $pid)
                    ->where('receiver_id', $userId)
                    ->whereNull('read_at')
                    ->count();

                $conversations->push([
                    'partner' => $partner,
                    'latest' => $latest,
                    'unread' => $unread,
                ]);
            }
        }

        // Sort by latest message
        $conversations = $conversations->sortByDesc(fn($c) => $c['latest']->created_at)->values();

        return view('messages.index', compact('conversations'));
    }

    /** Show individual conversation with another user + video call button */
    public function show(int $userId)
    {
        $partner = User::findOrFail($userId);
        $myId = Auth::id();

        $messages = Message::where(
            fn($q) =>
            $q->where('sender_id', $myId)->where('receiver_id', $userId)
        )->orWhere(
                fn($q) =>
                $q->where('sender_id', $userId)->where('receiver_id', $myId)
            )->with('sender')->orderBy('created_at')->get();

        // Mark received messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $myId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Generate deterministic Jitsi room name
        $roomName = 'fundbridge-' . min($myId, $userId) . '-' . max($myId, $userId);

        return view('messages.show', compact('partner', 'messages', 'roomName'));
    }

    /** Send a message */
    public function send(Request $request, int $userId)
    {
        $request->validate(['content' => 'required|string|max:2000']);

        $receiver = User::findOrFail($userId);

        $msg = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $userId,
            'content' => $request->content,
        ]);

        $msg->load('sender');
        $receiver->notify(new NewMessageNotification($msg));

        if ($request->expectsJson()) {
            return response()->json([
                'id' => $msg->id,
                'message' => $msg->content,
                'sender_id' => $msg->sender_id,
                'created_at' => $msg->created_at->format('H:i'),
            ]);
        }

        return back();
    }

    /** Polling endpoint: get new messages after a certain ID */
    public function poll(Request $request, int $userId)
    {
        $myId = Auth::id();
        $lastId = (int) $request->get('last_id', 0);

        $messages = Message::where(
            fn($q) =>
            $q->where('sender_id', $myId)->where('receiver_id', $userId)
        )->orWhere(
                fn($q) =>
                $q->where('sender_id', $userId)->where('receiver_id', $myId)
            )->where('id', '>', $lastId)
            ->with('sender')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'message' => $m->content,
                'sender_id' => $m->sender_id,
                'sender_name' => $m->sender->name,
                'created_at' => $m->created_at->format('H:i'),
                'mine' => $m->sender_id === $myId,
            ]);

        // Mark as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $myId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }
}
