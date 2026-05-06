<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('pages.home');
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function mission(): View
    {
        return view('pages.mission');
    }

    public function leadership(): View
    {
        return view('pages.leadership');
    }

    public function advocacy(): View
    {
        return view('pages.advocacy');
    }

    public function stories(): View
    {
        return view('pages.stories');
    }

    public function projects(): View
    {
        return view('pages.projects');
    }

    public function services(): View
    {
        return view('pages.services');
    }

    public function community(): View
    {
        return view('pages.community');
    }
}
