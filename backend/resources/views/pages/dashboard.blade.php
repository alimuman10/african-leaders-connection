@extends('layouts.app')

@section('title', 'Dashboard | African Leaders Connection')

@section('content')
    <section class="page-hero dashboard-hero">
        <div class="shell narrow">
            <p class="eyebrow">Member Dashboard</p>
            <h1>Welcome, {{ $user->name }}.</h1>
            <p>Your account is ready for future leadership resources, community tools, and member services.</p>
        </div>
    </section>

    <section class="section">
        <div class="shell dashboard-grid">
            <article class="info-card">
                <h2>Profile Summary</h2>
                <dl class="summary-list">
                    <div><dt>Name</dt><dd>{{ $user->name }}</dd></div>
                    <div><dt>Email</dt><dd>{{ $user->email }}</dd></div>
                    <div><dt>Phone</dt><dd>{{ $user->phone ?: 'Not provided' }}</dd></div>
                    <div><dt>Country</dt><dd>{{ $user->country ?: 'Not provided' }}</dd></div>
                    <div><dt>Organization</dt><dd>{{ $user->organization ?: 'Not provided' }}</dd></div>
                </dl>
            </article>

            <article class="info-card">
                <h2>My Account</h2>
                <p>Account settings, profile updates, and member preferences will live here as the platform grows.</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="button button-secondary" type="submit">Logout</button>
                </form>
            </article>

            <article class="info-card">
                <h2>Messages / Contact History</h2>
                <p>Your future service inquiries, partnership conversations, and platform messages can be organized here.</p>
            </article>
        </div>
    </section>
@endsection
