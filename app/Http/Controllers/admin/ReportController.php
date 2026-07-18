<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Investment;
use App\Models\InvestorInterest;
use App\Models\User;
use App\Models\Venture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Platform KPIs
        $stats = [
            'total_users'     => User::count(),
            'total_founders'  => User::where('role', 'founder')->count(),
            'total_investors' => User::where('role', 'investor')->count(),
            'total_ventures'  => Venture::count(),
            'active_ventures' => Venture::where('status', 'active')->count(),
            'total_campaigns' => Campaign::count(),
            'total_raised'    => Venture::sum('raised_amount'),
            'total_invested'  => Investment::sum('amount'),
            'total_interests' => InvestorInterest::count(),
        ];

        // Monthly signups (last 12 months)
        $monthlySignups = $this->monthlyCount(User::class, 12);

        // Monthly ventures created (last 12 months)
        $monthlyVentures = $this->monthlyCount(Venture::class, 12);

        // Monthly raised (from campaigns, last 12 months)
        $monthlyRaised = $this->monthlyRaised(12);

        // Top 10 ventures by raised amount
        $topVentures = Venture::with('founder')
            ->orderByDesc('raised_amount')
            ->take(10)
            ->get();

        // Top founders by total raised
        $topFounders = User::where('role', 'founder')
            ->withSum('ventures', 'raised_amount')
            ->orderByDesc('ventures_sum_raised_amount')
            ->take(8)
            ->get();

        // Sector distribution
        $sectorData = Venture::selectRaw('sector, count(*) as count')
            ->whereNotNull('sector')
            ->groupBy('sector')
            ->orderByDesc('count')
            ->pluck('count', 'sector')
            ->toArray();

        // Stage distribution
        $stageData = Venture::selectRaw('stage, count(*) as count')
            ->whereNotNull('stage')
            ->groupBy('stage')
            ->orderByDesc('count')
            ->pluck('count', 'stage')
            ->toArray();

        // Recent investments
        $recentInvestments = Investment::with(['investor', 'venture', 'campaign'])
            ->latest()
            ->take(10)
            ->get();

        // Interest level breakdown
        $interestBreakdown = InvestorInterest::selectRaw('interest_level, count(*) as count')
            ->groupBy('interest_level')
            ->pluck('count', 'interest_level')
            ->toArray();

        return view('admin.reports', compact(
            'stats',
            'monthlySignups',
            'monthlyVentures',
            'monthlyRaised',
            'topVentures',
            'topFounders',
            'sectorData',
            'stageData',
            'recentInvestments',
            'interestBreakdown'
        ));
    }

    /** JSON API for live stats refresh */
    public function apiStats()
    {
        return response()->json([
            'total_users'    => User::count(),
            'total_ventures' => Venture::count(),
            'total_raised'   => Venture::sum('raised_amount'),
            'total_invested' => Investment::sum('amount'),
        ]);
    }

    private function monthlyCount(string $model, int $months): array
    {
        $labels = [];
        $values = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $labels[] = $m->format('M Y');
            $values[] = $model::whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->count();
        }
        return ['labels' => $labels, 'values' => $values];
    }

    private function monthlyRaised(int $months): array
    {
        $labels = [];
        $values = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $labels[] = $m->format('M Y');
            $values[] = (float) Investment::whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->sum('amount');
        }
        return ['labels' => $labels, 'values' => $values];
    }
}
