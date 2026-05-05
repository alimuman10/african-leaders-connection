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
            <article class="info-card" id="profile">
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
                <h2>Account Status</h2>
                <p>Your account is active and ready for future role-based member features.</p>
                <dl class="summary-list">
                    <div><dt>Status</dt><dd>{{ ucfirst($user->status ?? 'active') }}</dd></div>
                    <div><dt>Email Verification</dt><dd>{{ $user->email_verified_at ? 'Verified' : 'Ready for verification' }}</dd></div>
                    <div><dt>Last Login</dt><dd>{{ $user->last_login_at?->format('M j, Y g:i A') ?: 'First session' }}</dd></div>
                </dl>
            </article>

            <article class="info-card">
                <h2>Quick Actions</h2>
                <div class="action-list">
                    <a class="button button-outline-dark" href="#profile">View Profile</a>
                    <a class="button button-outline-dark" href="{{ route('contact') }}">Contact Support</a>
                    <a class="button button-outline-dark" href="{{ route('home') }}">Return Home</a>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="button button-secondary" type="submit">Logout</button>
                </form>
            </article>
        </div>
    </section>
@endsection
