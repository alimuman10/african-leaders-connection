@extends('layouts.app')

@section('title', 'African Leaders Connection | Leadership. Unity. Progress.')
@section('description', 'Join African Leaders Connection, a professional Pan-African leadership network for service, innovation, and measurable progress.')

@section('content')
    <section class="hero-section">
        <div class="shell hero-grid">
            <div class="hero-copy">
                <p class="eyebrow">Pan-African Leadership Network</p>
                <h1>Connect with leaders building practical progress across Africa.</h1>
                <p>
                    African Leaders Connection brings leadership development, civic responsibility, digital innovation, and impact storytelling into one professional platform.
                </p>
                <div class="button-row">
                    <a class="button button-primary" href="{{ route('register') }}">Join the Network</a>
                    <a class="button button-secondary" href="{{ route('login') }}">Sign In</a>
                    <a class="button button-outline" href="#contact">Contact Us</a>
                </div>
            </div>
            <figure class="media-card hero-media">
                <img src="{{ asset('assets/images/african-leader-meeting-hero.png') }}" alt="African leader in a professional meeting representing service and practical progress">
            </figure>
        </div>
    </section>

    <section class="section">
        <div class="shell section-heading">
            <p class="eyebrow">What We Build</p>
            <h2>A credible platform for leadership, service, and institutional growth.</h2>
        </div>
        <div class="shell card-grid">
            <article class="info-card">
                <h3>Leadership Development</h3>
                <p>Programs, coaching, and practical frameworks for leaders who serve with discipline and accountability.</p>
            </article>
            <article class="info-card">
                <h3>Digital Visibility</h3>
                <p>Modern web presence, content systems, and visibility strategy for leaders and organizations.</p>
            </article>
            <article class="info-card">
                <h3>Impact Stories</h3>
                <p>Storytelling that makes progress visible, credible, and useful for emerging African leaders.</p>
            </article>
        </div>
    </section>

    <section class="section section-deep">
        <div class="shell split-layout">
            <figure class="media-card">
                <img src="{{ asset('assets/images/africa-map-bronze.png') }}" alt="Bronze map of Africa representing Pan-African connection">
            </figure>
            <div>
                <p class="eyebrow">Leadership. Unity. Progress.</p>
                <h2>A platform built for visibility, trust, and real collaboration.</h2>
                <p>
                    The African Leaders Connection brand brings together leadership ideas, practical services, advocacy, stories, and community pathways in one professional digital home.
                </p>
                <div class="button-row">
                    <a class="button button-primary" href="{{ route('advocacy') }}">Explore Advocacy</a>
                    <a class="button button-outline" href="{{ route('stories') }}">Read Stories</a>
                </div>
            </div>
        </div>
    </section>

    <section class="section section-muted" id="contact">
        <div class="shell contact-layout">
            <div>
                <p class="eyebrow">Contact Us</p>
                <h2>Start a professional conversation with African Leaders Connection.</h2>
                <p>
                    Use the form for partnerships, leadership services, speaking invitations, community initiatives, or platform inquiries.
                </p>
            </div>
            <x-contact-form />
        </div>
    </section>
@endsection
