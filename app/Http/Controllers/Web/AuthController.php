<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Auth\LoginRequest;
use App\Actions\Auth\AttemptLoginAction;
use App\Actions\Auth\LogoutAction;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request, AttemptLoginAction $attemptLogin)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if ($attemptLogin->run($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended('/')->with('success', 'You are now logged in!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request, LogoutAction $logout)
    {
        $logout->run();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out.');
    }
}
