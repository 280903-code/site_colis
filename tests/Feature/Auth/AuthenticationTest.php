<?php

namespace Tests\Feature\Auth;

use App\Models\PartnerApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Connexion')
            ->assertDontSee('<footer', false);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_admins_are_redirected_to_the_admin_dashboard_after_login(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->post('/login', [
            'email' => strtoupper($admin->email),
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->get(route('dashboard'))
            ->assertRedirect(route('admin.dashboard', absolute: false));

        $this->followingRedirects()
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_failed_admin_login_does_not_show_partner_verification_message(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => $admin->email,
                'password' => 'wrong-password',
            ])
            ->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertDontSee('confirmez d’abord votre adresse e-mail')
            ->assertDontSee(route('partner.verification.pending'));
    }

    public function test_failed_login_for_pending_partner_shows_verification_message(): void
    {
        PartnerApplication::create([
            'contact_name' => 'Partner Contact',
            'email' => 'partner@example.com',
            'password' => bcrypt('password'),
            'agency_name' => 'Partner Agency',
            'address' => 'Dakar',
            'opening_hours' => 'Lun-Sam, 9h-19h',
            'whatsapp' => '221770000001',
            'routes' => [['from' => 'SN', 'to' => 'FR']],
            'verification_token_hash' => hash('sha256', 'verification-token'),
            'expires_at' => now()->addDay(),
        ]);

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => 'PARTNER@example.com',
                'password' => 'password',
            ])
            ->assertRedirect(route('login'));

        $this->get(route('login'))
            ->assertSee('confirmez d’abord votre adresse e-mail')
            ->assertSee(route('partner.verification.pending'));
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
