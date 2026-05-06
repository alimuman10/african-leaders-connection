<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Services\SecurityAuditLogger;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, SecurityAuditLogger $audit): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $request->user()->forceFill([
            'last_login_at' => now(),
        ])->save();
        $audit->login($request->user(), $request);

        return redirect()->intended($request->user()->hasRole('Super Admin') ? '/admin/dashboard' : '/member/dashboard');
    }

    public function destroy(Request $request, SecurityAuditLogger $audit): RedirectResponse
    {
        $user = $request->user();
        $user?->tokens()->delete();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $audit->event('auth.logout', $user, 'info', $user, [], $request);

        return redirect()->route('login')->with('status', 'You have signed out securely.');
    }
}
