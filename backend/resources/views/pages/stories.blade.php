@extends('layouts.app')

@section('title', 'Stories | African Leaders Connection')

@section('content')
    <section class="page-hero">
        <div class="shell split-layout">
            <div>
                <p class="eyebrow">Impact Stories</p>
                <h1>Stories that show African leadership through action, courage, and measurable progress.</h1>
                <p>Strong stories make leadership tangible by showing how values become decisions, relationships, and outcomes.</p>
            </div>
            <figure class="media-card">
                <img src="{{ asset('assets/images/african-storytelling-fire.png') }}" alt="African storytelling around fire with elders and youth">
            </figure>
        </div>
    </section>

    <section class="section">
        <div class="shell card-grid">
            <article class="info-card"><h3>Community Builders</h3><p>Profiles of leaders who strengthen trust, participation, and local problem-solving.</p></article>
            <article class="info-card"><h3>Innovators With Purpose</h3><p>Stories of people using creativity, technology, and discipline to unlock opportunity.</p></article>
            <article class="info-card"><h3>Educators and Mentors</h3><p>Examples of leadership that multiplies impact by helping others rise.</p></article>
        </div>
    </section>
@endsection
