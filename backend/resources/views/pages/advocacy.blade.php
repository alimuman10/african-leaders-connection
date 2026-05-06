@extends('layouts.app')

@section('title', 'Advocacy | African Leaders Connection')

@section('content')
    <section class="page-hero">
        <div class="shell split-layout">
            <div>
                <p class="eyebrow">Advocacy</p>
                <h1>Advocacy rooted in service, responsibility, civic engagement, and practical progress.</h1>
                <p>We support leadership conversations that help communities move from awareness to action.</p>
            </div>
            <figure class="media-card">
                <img src="{{ asset('assets/images/advocacy-hero-progress.png') }}" alt="Clean modern African advocacy poster scene">
            </figure>
        </div>
    </section>

    <section class="section">
        <div class="shell card-grid">
            <article class="info-card"><h3>Leadership Accountability</h3><p>Encouraging leaders to act with clarity, humility, and responsibility.</p></article>
            <article class="info-card"><h3>Youth Civic Leadership</h3><p>Helping young leaders develop confidence, discipline, and service-minded influence.</p></article>
            <article class="info-card"><h3>Community Development</h3><p>Supporting practical conversations that turn local priorities into visible progress.</p></article>
        </div>
    </section>
@endsection
