<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // This is the method your route was looking for!
    public function loginView()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    // 1. Validate the form
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // 2. Try to log in (This uses Laravel's built-in session system)
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        // 3. Set the tenant context for the Global Scope
        session(['tenant_id' => Auth::user()->tenant_id]);

        // 4. Send them to the projects page
        return redirect()->intended('/projects');
    }

    // 5. If it fails, go back to login with an error
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
}

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}