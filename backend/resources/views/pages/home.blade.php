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
            <div class="hero-panel" aria-label="Platform highlights">
                <div>
                    <strong>Leadership</strong>
                    <span>Training, visibility, and responsible public influence.</span>
                </div>
                <div>
                    <strong>Innovation</strong>
                    <span>Digital systems and tools for modern institutions.</span>
                </div>
                <div>
                    <strong>Community</strong>
                    <span>Partnerships, mentorship, and story-led impact.</span>
                </div>
            </div>
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
