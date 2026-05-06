<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebAuthAndContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads(): void
    {
        $this->withoutVite();

        $this->get('/')
            ->assertOk()
            ->assertSee('Join the Network')
            ->assertSee('Contact Us');
    }

    public function test_user_can_register_login_view_dashboard_and_logout(): void
    {
        $this->withoutVite();

        $password = 'StrongPass#2026';

        $this->post('/register', [
            'name' => 'Aminata Kamara',
            'email' => 'aminata@example.com',
            'phone' => '+23279101090',
            'country' => 'Sierra Leone',
            'organization' => 'African Leaders Connection',
            'password' => $password,
            'password_confirmation' => $password,
        ])->assertRedirect('/member/dashboard');

        $this->assertAuthenticated();
        $this->get('/dashboard')->assertRedirect('/member/dashboard');
        $this->get('/member/dashboard')->assertOk();

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();

        $this->post('/login', [
            'email' => 'aminata@example.com',
            'password' => $password,
        ])->assertRedirect('/member/dashboard');

        $this->assertAuthenticated();
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->withoutVite();

        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_contact_form_stores_valid_messages(): void
    {
        $this->withoutVite();

        $this->post('/contact', [
            'name' => 'Daniel Mensah',
            'email' => 'daniel@example.com',
            'phone' => '+233000000000',
            'subject' => 'Partnership inquiry',
            'message' => 'I would like to discuss a leadership development partnership.',
        ])->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas(ContactMessage::class, [
            'name' => 'Daniel Mensah',
            'email' => 'daniel@example.com',
            'phone' => '+233000000000',
            'subject' => 'Partnership inquiry',
            'status' => 'pending',
        ]);
    }

    public function test_honeypot_blocks_spam_contact_submission(): void
    {
        $this->withoutVite();

        $this->from('/contact')->post('/contact', [
            'name' => 'Spam Bot',
            'email' => 'spam@example.com',
            'subject' => 'Automated message',
            'message' => 'This should not be stored because the honeypot is filled.',
            'website' => 'https://spam.example',
        ])->assertRedirect('/contact')
            ->assertSessionHasErrors('website');

        $this->assertDatabaseMissing(ContactMessage::class, [
            'email' => 'spam@example.com',
        ]);
    }
}
