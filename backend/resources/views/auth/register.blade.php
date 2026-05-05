@extends('layouts.guest')

@section('title', 'Sign Up | African Leaders Connection')

@section('content')
    <section class="auth-section">
        <div class="auth-card auth-card-wide">
            <p class="eyebrow">Join the Network</p>
            <h1>Create your account.</h1>
            <p>Start with a secure profile built for leadership, services, and future community access.</p>

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
