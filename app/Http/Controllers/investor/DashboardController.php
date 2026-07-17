<?php

namespace App\Http\Controllers\investor;

use App\Http\Controllers\Controller;
use App\Models\InvestorInterest;
use App\Models\Venture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ventures this investor has shown interest in
        $myInterests = InvestorInterest::where('investor_id', $user->id)
            ->with('venture.founder')
            ->latest()
            ->get();

        // Portfolio stats
        $totalTracked      = $myInterests->count();
        $highInterestCount = $myInterests->where('interest_level', 'high')->count();

        // Unread messages
        $unreadMessages = $user->receivedMessages()->whereNull('read_at')->count();

        // Discover: latest active ventures (excluding ones already interested in)
        $interestedVentureIds = $myInterests->pluck('venture_id');
        $discoverVentures = Venture::where('status', 'active')
            ->whereNotIn('id', $interestedVentureIds)
            ->with('founder')
            ->latest()
            ->take(9)
            ->get();

        // All active ventures count for stats
        $totalActiveVentures = Venture::where('status', 'active')->count();

        // Chart data: interest level distribution
        $interestChart = [
            'labels' => ['High', 'Medium', 'Low'],
            'values' => [
                $myInterests->where('interest_level', 'high')->count(),
                $myInterests->where('interest_level', 'medium')->count(),
                $myInterests->where('interest_level', 'low')->count(),
            ],
        ];

        return view('investor.dashboard', compact(
            'user',
            'myInterests',
            'totalTracked',
            'highInterestCount',
            'unreadMessages',
            'discoverVentures',
            'totalActiveVentures',
            'interestChart'
        ));
    }

    /** Mark interest in a venture */
    public function markInterest(Request $request, int $ventureId)
    {
        $request->validate([
            'interest_level' => 'required|in:low,medium,high',
            'note'           => 'nullable|string|max:500',
        ]);

        InvestorInterest::updateOrCreate(
            ['investor_id' => Auth::id(), 'venture_id' => $ventureId],
            ['interest_level' => $request->interest_level, 'note' => $request->note]
        );

        // Increment venture views
        Venture::where('id', $ventureId)->increment('views');

        return back()->with('status', 'Interest recorded successfully!');
    }
}
