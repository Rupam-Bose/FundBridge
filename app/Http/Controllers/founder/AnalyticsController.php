<?php

namespace App\Http\Controllers\founder;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\InvestorInterest;
use App\Models\Venture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user       = Auth::user();
        $ventures   = Venture::where('user_id', $user->id)->get();
        $ventureIds = $ventures->pluck('id');

        // Monthly raised (last 12 months)
        $monthlyRaised = $this->getMonthlyData($ventureIds, 12);

        // Monthly views (last 12 months) — simulated from venture creation dates
        $monthlyViews = $this->getMonthlyViews($ventureIds, 12);

        // Campaign performance
        $campaigns = Campaign::whereIn('venture_id', $ventureIds)
            ->with('venture')
            ->get();

        // Investor interest breakdown
        $interestBreakdown = InvestorInterest::whereIn('venture_id', $ventureIds)
            ->selectRaw('interest_level, count(*) as count')
            ->groupBy('interest_level')
            ->pluck('count', 'interest_level')
            ->toArray();

        // Sector distribution
        $sectorData = $ventures->groupBy('sector')
            ->map->count()
            ->toArray();

        // Top performing ventures by raised/goal ratio
        $topVentures = $ventures->sortByDesc(function ($v) {
            return $v->progressPercent();
        })->take(5);

        // Summary numbers
        $totalRaised       = $ventures->sum('raised_amount');
        $totalGoal         = $ventures->sum('goal_amount');
        $totalViews        = $ventures->sum('views');
        $totalInterests    = InvestorInterest::whereIn('venture_id', $ventureIds)->count();

        return view('founder.analytics', compact(
            'monthlyRaised',
            'monthlyViews',
            'campaigns',
            'interestBreakdown',
            'sectorData',
            'topVentures',
            'totalRaised',
            'totalGoal',
            'totalViews',
            'totalInterests',
            'ventures'
        ));
    }

    /** JSON endpoint for live chart refresh */
    public function apiStats()
    {
        $user       = Auth::user();
        $ventures   = Venture::where('user_id', $user->id)->get();
        $ventureIds = $ventures->pluck('id');

        return response()->json([
            'monthly_raised' => $this->getMonthlyData($ventureIds, 6),
            'total_raised'   => $ventures->sum('raised_amount'),
            'total_views'    => $ventures->sum('views'),
            'total_interests'=> InvestorInterest::whereIn('venture_id', $ventureIds)->count(),
        ]);
    }

    private function getMonthlyData($ventureIds, int $months): array
    {
        $labels = [];
        $values = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $labels[] = $m->format('M Y');
            $values[] = (float) Campaign::whereIn('venture_id', $ventureIds)
                ->whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->sum('raised');
        }
        return ['labels' => $labels, 'values' => $values];
    }

    private function getMonthlyViews($ventureIds, int $months): array
    {
        // Simulated: distribute total views across months proportionally
        $labels = [];
        $values = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $labels[] = $m->format('M Y');
            $values[] = Venture::whereIn('id', $ventureIds)
                ->whereYear('created_at', '<=', $m->year)
                ->count() * rand(20, 80); // proportional simulation
        }
        return ['labels' => $labels, 'values' => $values];
    }
}
