<?php

namespace App\Http\Controllers\investor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('investor.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'company_name' => 'nullable|string|max:150',
            'bio'          => 'nullable|string|max:500',
            'avatar'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } else {
            unset($data['avatar']);
        }

        $user->update($data);

        return back()->with('status', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('status', 'Password changed successfully!');
    }
}
