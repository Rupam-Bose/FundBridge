<?php

namespace App\Http\Controllers\investor;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Investment;
use App\Models\InvestorInterest;
use App\Models\Venture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    /** Show all campaigns from ventures the investor tracks */
    public function index(Request $request)
    {
        $trackedVentureIds = InvestorInterest::where('investor_id', Auth::id())
            ->pluck('venture_id');

        $query = Campaign::whereIn('venture_id', $trackedVentureIds)
            ->with(['venture.founder', 'investments' => fn($q) => $q->where('investor_id', Auth::id())]);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'goal_high'   => $query->orderByDesc('goal'),
            'most_raised' => $query->orderByDesc('raised'),
            'deadline'    => $query->orderBy('deadline'),
            default       => $query->latest(),
        };

        $campaigns = $query->paginate(12)->appends($request->query());

        // My investment summary
        $myStats = [
            'total_invested' => Investment::where('investor_id', Auth::id())->sum('amount'),
            'total_ventures' => $trackedVentureIds->count(),
            'total_campaigns' => $campaigns->total(),
            'investments_count' => Investment::where('investor_id', Auth::id())->count(),
        ];

        return view('investor.campaigns', compact('campaigns', 'myStats'));
    }

    /** Fund a specific campaign */
    public function invest(Request $request, int $campaignId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:99999999',
            'note'   => 'nullable|string|max:500',
        ]);

        $campaign = Campaign::with('venture')->findOrFail($campaignId);

        // Ensure investor tracks this venture
        $tracked = InvestorInterest::where('investor_id', Auth::id())
            ->where('venture_id', $campaign->venture_id)
            ->exists();

        if (!$tracked) {
            return back()->withErrors(['amount' => 'You must track this venture before investing.']);
        }

        // Create investment record
        Investment::create([
            'investor_id' => Auth::id(),
            'venture_id'  => $campaign->venture_id,
            'campaign_id' => $campaignId,
            'amount'      => $request->amount,
            'note'        => $request->note,
            'status'      => 'confirmed',
        ]);

        // Update campaign raised amount
        $campaign->increment('raised', $request->amount);

        // Sync venture raised_amount from all campaigns
        $totalRaised = Campaign::where('venture_id', $campaign->venture_id)->sum('raised');
        $campaign->venture->update(['raised_amount' => $totalRaised]);

        return back()->with('status', 'Investment of $' . number_format($request->amount, 2) . ' added to "' . $campaign->title . '"!');
    }

    /** API: get my investments list */
    public function apiMyInvestments()
    {
        $investments = Investment::where('investor_id', Auth::id())
            ->with(['campaign', 'venture'])
            ->latest()
            ->get();

        return response()->json($investments);
    }
}
