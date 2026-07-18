<?php

namespace App\Http\Controllers\founder;

use App\Http\Controllers\Controller;
use App\Models\Venture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VentureController extends Controller
{
    public function index()
    {
        $ventures = Venture::where('user_id', Auth::id())
            ->withCount(['campaigns', 'interests'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total'     => Venture::where('user_id', Auth::id())->count(),
            'active'    => Venture::where('user_id', Auth::id())->where('status', 'active')->count(),
            'completed' => Venture::where('user_id', Auth::id())->where('status', 'completed')->count(),
            'draft'     => Venture::where('user_id', Auth::id())->where('status', 'draft')->count(),
        ];

        return view('founder.ventures.index', compact('ventures', 'stats'));
    }

    public function create()
    {
        return view('founder.ventures.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:150',
            'description'   => 'nullable|string',
            'sector'        => 'nullable|string|max:100',
            'stage'         => 'nullable|string|max:100',
            'goal_amount'   => 'required|numeric|min:0',
            'status'        => 'required|in:active,paused,draft',
            'logo'          => 'nullable|image|max:2048',
            'pitch_deck'    => 'nullable|mimes:pdf,pptx,ppt|max:20480',
        ]);

        $data['user_id'] = Auth::id();
        $data['raised_amount'] = 0;

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('ventures/logos', 'public');
        }
        if ($request->hasFile('pitch_deck')) {
            $data['pitch_deck_path'] = $request->file('pitch_deck')->store('ventures/pitchdecks', 'public');
        }

        unset($data['logo'], $data['pitch_deck']);
        Venture::create($data);

        return redirect()->route('founder.ventures')
            ->with('status', 'Venture created successfully!');
    }

    public function edit(int $id)
    {
        $venture = Venture::where('user_id', Auth::id())->findOrFail($id);
        return view('founder.ventures.edit', compact('venture'));
    }

    public function update(Request $request, int $id)
    {
        $venture = Venture::where('user_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string',
            'sector'      => 'nullable|string|max:100',
            'stage'       => 'nullable|string|max:100',
            'goal_amount' => 'required|numeric|min:0',
            'status'      => 'required|in:active,paused,draft,completed',
            'logo'        => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($venture->logo_path) Storage::disk('public')->delete($venture->logo_path);
            $data['logo_path'] = $request->file('logo')->store('ventures/logos', 'public');
        }
        unset($data['logo']);

        $venture->update($data);

        return redirect()->route('founder.ventures')
            ->with('status', 'Venture updated successfully!');
    }

    public function destroy(int $id)
    {
        $venture = Venture::where('user_id', Auth::id())->findOrFail($id);
        $venture->delete();

        return redirect()->route('founder.ventures')
            ->with('status', 'Venture deleted.');
    }
}
