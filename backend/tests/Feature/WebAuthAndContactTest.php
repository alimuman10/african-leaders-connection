<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
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
        $this->get('/member/dashboard')->assertRedirect('/verify-email');

        $user = User::where('email', 'aminata@example.com')->firstOrFail();
        $user->forceFill(['email_verified_at' => now(), 'status' => 'active'])->save();

        $this->actingAs($user->fresh());
        $this->get('/member/dashboard')->assertOk();

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();

        $this->post('/login', [
            'email' => 'aminata@example.com',
            'password' => $password,
        ])->assertRedirect('/member/dashboard');

        $this->assertAuthenticated();
    }

    public function test_public_registration_cannot_use_super_admin_email(): void
    {
        $this->withoutVite();

        $this->from('/register')->post('/register', [
            'name' => 'Blocked Admin',
            'email' => config('auth_security.super_admin_email'),
            'password' => 'StrongPass#2026',
            'password_confirmation' => 'StrongPass#2026',
        ])->assertRedirect('/register')
            ->assertSessionHasErrors('email');

        $this->assertDatabaseMissing(User::class, [
            'email' => config('auth_security.super_admin_email'),
        ]);
    }

    public function test_super_admin_logs_in_to_admin_dashboard_and_member_cannot_open_admin_dashboard(): void
    {
        $this->withoutVite();

        Role::firstOrCreate(['name' => 'Member', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $member = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $member->assignRole('Member');

        $this->actingAs($member)
            ->get('/admin/dashboard')
            ->assertForbidden();

        auth()->logout();

        $superAdmin = User::factory()->create([
            'email' => config('auth_security.super_admin_email'),
            'password' => Hash::make('StrongPass#2026'),
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $superAdmin->assignRole('Super Admin');

        $this->post('/login', [
            'email' => $superAdmin->email,
            'password' => 'StrongPass#2026',
        ])->assertRedirect('/admin/dashboard');
    }

    public function test_api_user_management_cannot_assign_super_admin_role(): void
    {
        Role::firstOrCreate(['name' => 'Member', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $superAdmin = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $superAdmin->assignRole('Super Admin');

        $member = User::factory()->create([
            'email_verified_at' => now(),
            'status' => 'active',
        ]);
        $member->assignRole('Member');

        Sanctum::actingAs($superAdmin);

        $this->postJson('/api/admin/users', [
            'name' => 'Injected Admin',
            'email' => 'injected-admin@example.com',
            'roles' => ['Super Admin'],
        ])->assertUnprocessable();

        $this->postJson("/api/admin/users/{$member->id}/roles/assign", [
            'role' => 'Super Admin',
        ])->assertUnprocessable();
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
