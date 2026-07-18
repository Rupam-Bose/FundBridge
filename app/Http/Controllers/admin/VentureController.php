<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Venture;
use Illuminate\Http\Request;

class VentureController extends Controller
{
    public function index(Request $request)
    {
        $query = Venture::with(['founder', 'campaigns'])
            ->withCount(['campaigns', 'interests']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('sector', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sector')) {
            $query->where('sector', $request->sector);
        }

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'most_raised'    => $query->orderByDesc('raised_amount'),
            'most_interests' => $query->orderByDesc('interests_count'),
            'goal_high'      => $query->orderByDesc('goal_amount'),
            default          => $query->latest(),
        };

        $ventures  = $query->paginate(15)->appends($request->query());
        $sectors   = Venture::distinct()->pluck('sector')->filter()->sort()->values();
        $stats = [
            'total'     => Venture::count(),
            'active'    => Venture::where('status', 'active')->count(),
            'paused'    => Venture::where('status', 'paused')->count(),
            'completed' => Venture::where('status', 'completed')->count(),
            'total_raised' => Venture::sum('raised_amount'),
        ];

        return view('admin.ventures', compact('ventures', 'sectors', 'stats'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:active,paused,draft,completed']);
        Venture::findOrFail($id)->update(['status' => $request->status]);
        return back()->with('status', 'Venture status updated.');
    }

    public function destroy(int $id)
    {
        Venture::findOrFail($id)->delete();
        return back()->with('status', 'Venture removed from platform.');
    }
}
