@extends('layouts.guest')

@section('title', 'Sign In | African Leaders Connection')

@section('content')
    <section class="auth-section">
        <div class="auth-card">
            <a class="auth-brand" href="{{ route('home') }}" aria-label="African Leaders Connection home">
                <img src="{{ asset('assets/images/brand-icon-square.png') }}" alt="">
                <span>African Leaders Connection</span>
            </a>
            <p class="eyebrow">Sign In</p>
            <h1>Welcome back.</h1>
            <p>Access your African Leaders Connection account securely.</p>

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    Please check your email and password, then try again.
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf
                <label>
                    <span>Email address</span>
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    <x-field-error name="email" />
                </label>

                <label>
                    <span>Password</span>
                    <input type="password" name="password" required autocomplete="current-password">
                    <x-field-error name="password" />
                </label>

                <label class="check-row">
                    <input type="checkbox" name="remember" value="1">
                    <span>Remember me</span>
                </label>

                <button class="button button-primary" type="submit">Sign In</button>
            </form>

            <p class="auth-switch">New to the network? <a href="{{ route('register') }}">Create an account</a></p>
        </div>
    </section>
@endsection
