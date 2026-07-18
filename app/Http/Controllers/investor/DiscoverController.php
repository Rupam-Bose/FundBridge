<?php

namespace App\Http\Controllers\investor;

use App\Http\Controllers\Controller;
use App\Models\InvestorInterest;
use App\Models\Venture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscoverController extends Controller
{
    public function index(Request $request)
    {
        $query = Venture::where('status', 'active')->with('founder');

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhere('description', 'like', '%'.$request->search.'%')
                  ->orWhere('sector', 'like', '%'.$request->search.'%');
            });
        }

        // Sector filter
        if ($request->filled('sector')) {
            $query->where('sector', $request->sector);
        }

        // Stage filter
        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'most_funded'  => $query->orderByDesc('raised_amount'),
            'most_viewed'  => $query->orderByDesc('views'),
            'goal_asc'     => $query->orderBy('goal_amount'),
            default        => $query->latest(),
        };

        $ventures = $query->paginate(9)->appends($request->query());

        // Sectors for filter dropdown
        $sectors = Venture::where('status', 'active')->distinct()->pluck('sector')->filter()->sort()->values();
        $stages  = Venture::where('status', 'active')->distinct()->pluck('stage')->filter()->sort()->values();

        // Investor's already-tracked venture IDs
        $trackedIds = InvestorInterest::where('investor_id', Auth::id())->pluck('venture_id');

        return view('investor.discover', compact('ventures', 'sectors', 'stages', 'trackedIds'));
    }

    /** API: return paginated ventures as JSON */
    public function apiList(Request $request)
    {
        $ventures = Venture::where('status', 'active')
            ->with('founder:id,name,company_name')
            ->when($request->filled('search'), fn($q) =>
                $q->where('title', 'like', '%'.$request->search.'%')
                  ->orWhere('sector', 'like', '%'.$request->search.'%')
            )
            ->when($request->filled('sector'), fn($q) =>
                $q->where('sector', $request->sector)
            )
            ->latest()
            ->paginate(9);

        return response()->json($ventures);
    }
}
