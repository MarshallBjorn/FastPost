<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(UserRequest $request)
    {
        if ($request->form_type !== 'register') {
            return redirect()->back()->withInput();
        }

        $user = $request->validated();
        $user['password'] = bcrypt($user['password']);

        $user = User::create($user);

        Auth::login($user);

        return redirect('/');
    }

    public function login(Request $request)
    {
        if ($request->form_type !== 'login') {
            return redirect()->back()->withInput();
        }

        $credentials = $request->validate([
            'login_email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $credentials['login_email'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->with('login_error', 'Invalid credentials.')->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/auth');
    }
}
