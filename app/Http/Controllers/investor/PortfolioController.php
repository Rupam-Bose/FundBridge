<?php

namespace App\Http\Controllers\investor;

use App\Http\Controllers\Controller;
use App\Models\InvestorInterest;
use App\Models\Venture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    public function index(Request $request)
    {
        $query = InvestorInterest::where('investor_id', Auth::id())
            ->with(['venture.founder', 'venture.campaigns']);

        if ($request->filled('interest_level')) {
            $query->where('interest_level', $request->interest_level);
        }
        if ($request->filled('search')) {
            $query->whereHas('venture', fn($q) =>
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhere('sector', 'like', '%'.$request->search.'%')
            );
        }

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'high_interest' => $query->orderByRaw("CASE interest_level WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END"),
            'low_interest'  => $query->orderByRaw("CASE interest_level WHEN 'low' THEN 1 WHEN 'medium' THEN 2 WHEN 'high' THEN 3 ELSE 4 END"),
            default         => $query->latest(),
        };

        $interests = $query->paginate(12)->appends($request->query());

        $stats = [
            'total'      => InvestorInterest::where('investor_id', Auth::id())->count(),
            'high'       => InvestorInterest::where('investor_id', Auth::id())->where('interest_level', 'high')->count(),
            'medium'     => InvestorInterest::where('investor_id', Auth::id())->where('interest_level', 'medium')->count(),
            'low'        => InvestorInterest::where('investor_id', Auth::id())->where('interest_level', 'low')->count(),
        ];

        // Chart: interest level breakdown
        $interestChart = [
            'labels' => ['High', 'Medium', 'Low'],
            'values' => [$stats['high'], $stats['medium'], $stats['low']],
        ];

        // Chart: Sector distribution in portfolio
        $sectors = InvestorInterest::where('investor_id', Auth::id())
            ->with('venture')
            ->get()
            ->groupBy(fn($i) => $i->venture->sector ?? 'Unknown')
            ->map->count()
            ->toArray();

        $sectorChart = [
            'labels' => array_keys($sectors),
            'values' => array_values($sectors),
        ];

        return view('investor.portfolio', compact('interests', 'stats', 'interestChart', 'sectorChart'));
    }

    /** Remove from portfolio */
    public function remove(int $ventureId)
    {
        InvestorInterest::where('investor_id', Auth::id())
            ->where('venture_id', $ventureId)
            ->delete();

        return back()->with('status', 'Venture removed from portfolio.');
    }

    /** Update interest level */
    public function updateInterest(Request $request, int $ventureId)
    {
        $request->validate(['interest_level' => 'required|in:low,medium,high']);

        InvestorInterest::where('investor_id', Auth::id())
            ->where('venture_id', $ventureId)
            ->update(['interest_level' => $request->interest_level, 'note' => $request->note]);

        return back()->with('status', 'Interest level updated!');
    }
}
