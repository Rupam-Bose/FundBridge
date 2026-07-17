<?php

namespace App\Http\Controllers\founder;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\InvestorInterest;
use App\Models\Message;
use App\Models\Venture;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fetch all ventures belonging to this founder
        $ventures = Venture::where('user_id', $user->id)->get();

        // Total raised across all ventures
        $totalRaised = $ventures->sum('raised_amount');

        // Active campaigns across all ventures
        $activeCampaignsCount = Campaign::whereIn(
            'venture_id',
            $ventures->pluck('id')
        )->where('status', 'active')->count();

        // Total investor views across all ventures
        $totalViews = $ventures->sum('views');

        // Unread messages
        $unreadMessages = $user->receivedMessages()->whereNull('read_at')->count();

        // Recent campaigns (latest 10) with venture info
        $recentCampaigns = Campaign::whereIn('venture_id', $ventures->pluck('id'))
            ->with('venture')
            ->latest()
            ->take(10)
            ->get();

        // Recent investor activity on this founder's ventures
        $recentInterests = InvestorInterest::whereIn('venture_id', $ventures->pluck('id'))
            ->with(['investor', 'venture'])
            ->latest()
            ->take(8)
            ->get();

        // Chart data: raised per month for last 6 months (from campaigns)
        $chartData = $this->getMonthlyRaisedData($ventures->pluck('id')->toArray());

        return view('founder.dashboard', compact(
            'user',
            'ventures',
            'totalRaised',
            'activeCampaignsCount',
            'totalViews',
            'unreadMessages',
            'recentCampaigns',
            'recentInterests',
            'chartData'
        ));
    }

    /**
     * Build monthly fundraising data for Chart.js
     */
    private function getMonthlyRaisedData(array $ventureIds): array
    {
        $labels = [];
        $values = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M Y');

            $raised = Campaign::whereIn('venture_id', $ventureIds)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('raised');

            $values[] = (float) $raised;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
