@extends('layouts.app')

@section('title', 'Community | African Leaders Connection')

@section('content')
    <section class="page-hero">
        <div class="shell split-layout">
            <div>
                <p class="eyebrow">Community</p>
                <h1>A professional network for leaders, learners, builders, and community-minded partners.</h1>
                <p>Community is where leadership becomes relationship, mentorship, accountability, and shared progress.</p>
            </div>
            <figure class="media-card">
                <img src="{{ asset('assets/images/african-leader-meeting.png') }}" alt="African leaders meeting and collaborating">
            </figure>
        </div>
    </section>

    <section class="section">
        <div class="shell card-grid">
            <article class="info-card"><h3>Join the Network</h3><p>Create an account and prepare for future community resources and member opportunities.</p></article>
            <article class="info-card"><h3>Mentorship</h3><p>Support emerging leaders through guidance, practical examples, and shared experience.</p></article>
            <article class="info-card"><h3>Partnerships</h3><p>Collaborate on programs, events, training, digital tools, and community impact work.</p></article>
        </div>
    </section>
@endsection
