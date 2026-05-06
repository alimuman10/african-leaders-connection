@extends('layouts.app')

@section('title', 'Leadership | African Leaders Connection')

@section('content')
    <section class="page-hero">
        <div class="shell narrow">
            <p class="eyebrow">Leadership Philosophy</p>
            <h1>Leadership that serves with character, responsibility, and disciplined action.</h1>
            <p>The platform promotes leadership rooted in public service, trust, clear communication, and measurable outcomes.</p>
        </div>
    </section>

    <section class="section">
        <div class="shell split-layout">
            <figure class="media-card">
                <img src="{{ asset('assets/images/hero-pan-african-leadership.jpg') }}" alt="Pan-African leadership gathering">
            </figure>
            <div class="card-grid single-column">
                <article class="info-card">
                    <h3>Responsible Influence</h3>
                    <p>Leadership should elevate people, strengthen institutions, and make better decisions possible.</p>
                </article>
                <article class="info-card">
                    <h3>Capacity Building</h3>
                    <p>Training, mentorship, coaching, and practical frameworks help leaders move from intention to execution.</p>
                </article>
                <article class="info-card">
                    <h3>Public-Minded Service</h3>
                    <p>Credible leadership is measured by the value it creates for communities, not only the position it holds.</p>
                </article>
            </div>
        </div>
    </section>
@endsection
