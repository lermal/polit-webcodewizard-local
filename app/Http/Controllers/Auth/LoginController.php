<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login', ['navbarTheme' => 'navbar-light']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Проверяем, только если email НЕ подтвержден
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')
                    ->with('warning', 'Пожалуйста, подтвердите свой email адрес перед входом.');
            }

            // Если email подтвержден, перенаправляем на dashboard
            return redirect()->intended(route('user.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Предоставленные учетные данные не соответствуют нашим записям.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('user.dashboard');
    }
}
