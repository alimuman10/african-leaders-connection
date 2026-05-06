@extends('layouts.app')

@section('title', 'About | African Leaders Connection')

@section('content')
    <section class="page-hero">
        <div class="shell narrow">
            <p class="eyebrow">About the Platform</p>
            <h1>African Leaders Connection exists to strengthen leadership visibility and community-centered progress.</h1>
            <p>
                The platform supports leaders, institutions, and communities through leadership development, digital innovation, advocacy, services, and credible impact storytelling.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="shell split-layout">
            <figure class="media-card">
                <img src="{{ asset('assets/images/profile.jpg') }}" alt="African Leaders Connection founder profile portrait">
            </figure>
            <div class="card-grid single-column">
                <article class="info-card">
                    <h3>Mission</h3>
                    <p>To connect and equip leaders who turn values into practical service, responsible institutions, and measurable progress.</p>
                </article>
                <article class="info-card">
                    <h3>Vision</h3>
                    <p>A stronger African leadership ecosystem where visibility, capacity, and collaboration help communities move forward.</p>
                </article>
                <article class="info-card">
                    <h3>Values</h3>
                    <p>Service, unity, accountability, innovation, and public-minded leadership.</p>
                </article>
            </div>
        </div>
    </section>
@endsection
