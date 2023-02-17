<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Mail\ResetPassword;
use App\Models\PasswordResets;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

        if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password, 'role' => User::ROLE_USER], $request->remember)) {
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
            if (Auth::guard('user')->login($user)) {
                $request->session()->regenerate();
            }
        }
        return redirect(route('home'));
    }

    public function logout(Request $request) {
        Auth::guard('user')->logout();

        $request->session()->invalidate();

        return redirect(route('show.login'));
    }

    public function showResetForm() {
        return view('auth.passwords.email');
    }

    public function sendResetMail(Request $request) {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        if (!User::where('email', $request->email)->first()) {
            return back()->withErrors([
                'email' => 'This email has not been registered',
            ]);
        }
        $token = Str::random(32);
        PasswordResets::insert(['email' => $request->email, 'token' => $token]);

        Mail::to($request->email)->send(new ResetPassword(env('APP_NAME', 'localhost') . '/reset-password' . '?token=' . $token));
        return $this->successResponse('Email sent', 'Success');
    }
}
