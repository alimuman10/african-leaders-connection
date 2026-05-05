@extends('layouts.app')

@section('title', 'Contact | African Leaders Connection')

@section('content')
    <section class="page-hero">
        <div class="shell narrow">
            <p class="eyebrow">Contact</p>
            <h1>Reach African Leaders Connection for partnerships, services, and collaboration.</h1>
            <p>Every submission is securely validated and stored for professional follow-up.</p>
        </div>
    </section>

    <section class="section section-muted">
        <div class="shell contact-layout">
            <div class="info-card">
                <h2>Let us know how we can help.</h2>
                <p>Share enough detail for the team to understand your goals, timeline, and the kind of support you need.</p>
                <p><strong>Email:</strong> mansarayalimu903@gmail.com</p>
                <p><strong>Phone:</strong> +232 79 101090</p>
            </div>
            <x-contact-form />
        </div>
    </section>
@endsection
