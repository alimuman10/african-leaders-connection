@extends('layouts.app')

@section('title', 'Sign In | African Leaders Connection')

@section('content')
    <section class="auth-section">
        <div class="auth-card">
            <p class="eyebrow">Sign In</p>
            <h1>Welcome back.</h1>
            <p>Access your African Leaders Connection account securely.</p>

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
