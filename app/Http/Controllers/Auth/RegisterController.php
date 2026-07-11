<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|min:8',
            'role' => 'required|in:founder,investor',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(
                $request->password
            ),
            'role' => $request->role,
            'company_name' =>
                $request->company_name,
        ]);

        Auth::login($user);
        if ($user->role == 'founder') {
            return redirect()
                ->route('founder.dashboard');
        }
        return redirect()
            ->route('investor.dashboard');
    }
}
