<?php

namespace App\Http\Controllers;

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

        if (Auth::guard()->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            $request->session()->regenerate();
            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ])->onlyInput('email');
    }

    public function showRegister() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'age' => ['integer'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $params = $request->all();

        $user = User::create([
            'name' => $params['name'],
            'age' => $params['age'],
            'email' => $params['email'],
            'password' => Hash::make($params['password']),
            'role' => User::ROLE_USER,
        ]);
        if ($user) {
            if (Auth::guard()->attempt(['email' => $request->email, 'password' => $request->password])) {
                $request->session()->regenerate();
            }
        }
        return redirect('/');
    }

    public function logout(Request $request) {
        Auth::guard()->logout();

        $request->session()->invalidate();

        return redirect('/');
    }
}
