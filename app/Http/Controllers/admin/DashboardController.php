<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Message;
use App\Models\User;
use App\Models\Venture;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers     = User::count();
        $totalFounders  = User::where('role', 'founder')->count();
        $totalInvestors = User::where('role', 'investor')->count();
        $totalAdmins    = User::where('role', 'admin')->count();
        $totalVentures  = Venture::count();
        $totalCampaigns = Campaign::count();
        $totalMessages  = Message::count();
        $totalRaised    = Venture::sum('raised_amount');

        // Recent registered users
        $recentUsers = User::latest()->take(8)->get();

        // Sector distribution for chart
        $sectors = Venture::selectRaw('sector, count(*) as count')
            ->whereNotNull('sector')
            ->groupBy('sector')
            ->pluck('count', 'sector')
            ->toArray();

        $sectorChart = [
            'labels' => array_keys($sectors),
            'values' => array_values($sectors),
        ];

        // Monthly new users for chart (last 6 months)
        $userGrowthChart = $this->getMonthlyUserGrowth();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalFounders',
            'totalInvestors',
            'totalAdmins',
            'totalVentures',
            'totalCampaigns',
            'totalMessages',
            'totalRaised',
            'recentUsers',
            'sectorChart',
            'userGrowthChart'
        ));
    }

    private function getMonthlyUserGrowth(): array
    {
        $labels = [];
        $values = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $values[] = User::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return ['labels' => $labels, 'values' => $values];
    }
}
