<?php

namespace App\Http\Controllers\founder;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Venture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CampaignController extends Controller
{
    public function index()
    {
        $ventureIds = Venture::where('user_id', Auth::id())->pluck('id');

        $campaigns = Campaign::whereIn('venture_id', $ventureIds)
            ->with('venture')
            ->latest()
            ->paginate(10);

        $ventures = Venture::where('user_id', Auth::id())
            ->where('status', '!=', 'completed')
            ->get();

        $stats = [
            'total'     => Campaign::whereIn('venture_id', $ventureIds)->count(),
            'active'    => Campaign::whereIn('venture_id', $ventureIds)->where('status', 'active')->count(),
            'completed' => Campaign::whereIn('venture_id', $ventureIds)->where('status', 'completed')->count(),
            'total_raised' => Campaign::whereIn('venture_id', $ventureIds)->sum('raised'),
        ];

        return view('founder.campaigns.index', compact('campaigns', 'ventures', 'stats'));
    }

    public function create()
    {
        $ventures = Venture::where('user_id', Auth::id())
            ->where('status', '!=', 'completed')
            ->get();

        if ($ventures->isEmpty()) {
            return redirect()->route('founder.ventures')
                ->with('error', 'You need to create a venture first before launching a campaign.');
        }

        return view('founder.campaigns.create', compact('ventures'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'venture_id'  => 'required|exists:ventures,id',
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string',
            'goal'        => 'required|numeric|min:1',
            'raised'      => 'nullable|numeric|min:0',
            'deadline'    => 'nullable|date|after:today',
            'status'      => 'required|in:active,paused,completed',
        ]);

        // Ensure venture belongs to this founder
        Venture::where('user_id', Auth::id())->findOrFail($data['venture_id']);

        $data['raised'] = $data['raised'] ?? 0;
        $campaign = Campaign::create($data);

        // Notify investors who track this venture
        $campaign->notifyTrackedInvestors();

        // Sync venture raised_amount
        $this->syncVentureRaised($data['venture_id']);

        return redirect()->route('founder.campaigns')
            ->with('status', 'Campaign created successfully!');
    }

    public function update(Request $request, int $id)
    {
        $ventureIds = Venture::where('user_id', Auth::id())->pluck('id');
        $campaign   = Campaign::whereIn('venture_id', $ventureIds)->findOrFail($id);

        $data = $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string',
            'goal'        => 'required|numeric|min:1',
            'raised'      => 'nullable|numeric|min:0',
            'deadline'    => 'nullable|date',
            'status'      => 'required|in:active,paused,completed',
        ]);

        $data['raised'] = $data['raised'] ?? $campaign->raised;
        $campaign->update($data);
        $this->syncVentureRaised($campaign->venture_id);

        return back()->with('status', 'Campaign updated!');
    }

    public function destroy(int $id)
    {
        $ventureIds = Venture::where('user_id', Auth::id())->pluck('id');
        $campaign   = Campaign::whereIn('venture_id', $ventureIds)->findOrFail($id);
        $vid        = $campaign->venture_id;
        $campaign->delete();
        $this->syncVentureRaised($vid);

        return back()->with('status', 'Campaign deleted.');
    }

    private function syncVentureRaised(int $ventureId): void
    {
        $total = Campaign::where('venture_id', $ventureId)->sum('raised');
        Venture::where('id', $ventureId)->update(['raised_amount' => $total]);
    }
}
