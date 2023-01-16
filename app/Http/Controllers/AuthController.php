<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            $request->session()->regenerate();
            return redirect(route('home'));
        }

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ])->onlyInput('email');
    }

    public function showRegister() {
        return view('auth.register');
    }

    public function register(RegisterRequest $request) {
        $user = User::create([
            'name' => $request?->name,
            'age' => $request?->age,
            'email' => $request?->email,
            'password' => Hash::make($request?->email),
            'role' => User::ROLE_USER,
        ]);
        if ($user) {
            if (Auth::login($user)) {
                $request->session()->regenerate();
            }
        }
        return redirect(route('home'));
    }

    public function logout(Request $request) {
        Auth::logout();

        $request->session()->invalidate();

        return redirect(route('show.login'));
    }
}
