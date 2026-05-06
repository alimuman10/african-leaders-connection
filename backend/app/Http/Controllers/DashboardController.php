<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        return redirect($request->user()->hasRole('Super Admin') ? '/admin/dashboard' : '/member/dashboard');
    }
}
