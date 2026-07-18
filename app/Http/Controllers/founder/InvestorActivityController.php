<?php

namespace App\Http\Controllers\founder;

use App\Http\Controllers\Controller;
use App\Models\InvestorInterest;
use App\Models\Venture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestorActivityController extends Controller
{
    public function index(Request $request)
    {
        $user       = Auth::user();
        $ventureIds = Venture::where('user_id', $user->id)->pluck('id');

        $query = InvestorInterest::whereIn('venture_id', $ventureIds)
            ->with(['investor', 'venture']);

        if ($request->filled('interest_level')) {
            $query->where('interest_level', $request->interest_level);
        }
        if ($request->filled('venture_id')) {
            $query->where('venture_id', $request->venture_id);
        }
        if ($request->filled('search')) {
            $query->whereHas('investor', fn($q) =>
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
            );
        }

        $interests = $query->latest()->paginate(15);

        $ventures = Venture::where('user_id', $user->id)->get();

        $stats = [
            'total'  => InvestorInterest::whereIn('venture_id', $ventureIds)->count(),
            'high'   => InvestorInterest::whereIn('venture_id', $ventureIds)->where('interest_level', 'high')->count(),
            'medium' => InvestorInterest::whereIn('venture_id', $ventureIds)->where('interest_level', 'medium')->count(),
            'low'    => InvestorInterest::whereIn('venture_id', $ventureIds)->where('interest_level', 'low')->count(),
        ];

        return view('founder.investor-activities', compact('interests', 'ventures', 'stats'));
    }
}
