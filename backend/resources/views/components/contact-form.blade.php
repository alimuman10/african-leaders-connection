<form class="form-card" method="POST" action="{{ route('contact.store') }}" novalidate>
    @csrf
    <input class="honeypot" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">

    <div class="form-grid">
        <label>
            <span>Full name</span>
            <input name="name" value="{{ old('name') }}" required autocomplete="name">
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
            <span>Subject</span>
            <input name="subject" value="{{ old('subject') }}" required>
            <x-field-error name="subject" />
        </label>
    </div>

    <label>
        <span>Message</span>
        <textarea name="message" rows="6" required>{{ old('message') }}</textarea>
        <x-field-error name="message" />
    </label>

    <button class="button button-primary" type="submit">Send Message</button>
</form>
