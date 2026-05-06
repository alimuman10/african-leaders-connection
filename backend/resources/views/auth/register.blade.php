@extends('layouts.guest')

@section('title', 'Sign Up | African Leaders Connection')

@section('content')
    <section class="auth-section">
        <div class="auth-card auth-card-wide">
            <a class="auth-brand" href="{{ route('home') }}" aria-label="African Leaders Connection home">
                <img src="{{ asset('assets/images/brand-icon-square.png') }}" alt="">
                <span>African Leaders Connection</span>
            </a>
            <p class="eyebrow">Join the Network</p>
            <h1>Create your account.</h1>
            <p>Start with a secure profile built for leadership, services, and future community access.</p>

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    Please review the highlighted fields and try again.
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" novalidate>
                @csrf
                <div class="form-grid">
                    <label>
                        <span>Full name</span>
                        <input name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        <x-field-error name="name" />
                    </label>

                    <label>
                        <span>Email address</span>
                        <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                        <x-field-error name="email" />
                    </label>
                </div>

                <div class="form-grid">
                    <label>
                        <span>Phone number <em>optional</em></span>
                        <input name="phone" value="{{ old('phone') }}" autocomplete="tel">
                        <x-field-error name="phone" />
                    </label>

                    <label>
                        <span>Country <em>optional</em></span>
                        <input name="country" value="{{ old('country') }}" autocomplete="country-name">
                        <x-field-error name="country" />
                    </label>
                </div>

                <label>
                    <span>Organization / institution <em>optional</em></span>
                    <input name="organization" value="{{ old('organization') }}" autocomplete="organization">
                    <x-field-error name="organization" />
                </label>

                <div class="form-grid">
                    <label>
                        <span>Password</span>
                        <input type="password" name="password" required autocomplete="new-password">
                        <small class="field-help">Use at least 8 characters with uppercase, lowercase, and a number.</small>
                        <x-field-error name="password" />
                    </label>

                    <label>
                        <span>Confirm password</span>
                        <input type="password" name="password_confirmation" required autocomplete="new-password">
                    </label>
                </div>

                <button class="button button-primary" type="submit">Create Account</button>
            </form>

            <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
        </div>
    </section>
@endsection
