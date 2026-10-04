<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\AgencyRoute;
use App\Models\User;
use App\Notifications\NewPartnerNotification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PartnerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_registration_form_can_be_rendered(): void
    {
        $this->get(route('partner.register.form'))
            ->assertOk()
            ->assertSee('Devenir partenaire');
    }

    public function test_partner_registration_creates_user_agency_and_routes(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->post(route('partner.register'), [
            'contact_name' => 'Partner Contact',
            'email' => 'PARTNER@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agency_name' => 'Partner Agency',
            'address' => 'Dakar',
            'opening_hours' => 'Lun-Sam, 9h-19h',
            'whatsapp' => '+221 77 000 00 01',
            'routes' => [
                json_encode(['from' => 'SN', 'to' => 'FR']),
            ],
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $user = User::where('email', 'partner@example.com')->firstOrFail();
        $agency = Agency::where('user_id', $user->id)->firstOrFail();
        $agencyRoute = AgencyRoute::where('agency_id', $agency->id)->firstOrFail();

        $this->assertModelExists($user);
        $this->assertModelExists($agency);
        $this->assertModelExists($agencyRoute);
        $this->assertSame('pending', $agency->status);
        $this->assertSame('SN', $agencyRoute->from_country);
        $this->assertSame('FR', $agencyRoute->to_country);

        Notification::assertSentTo($user, VerifyEmail::class);
        Notification::assertSentTo($admin, NewPartnerNotification::class);
    }

    public function test_partner_registration_rejects_invalid_route_data_without_creating_records(): void
    {
        $response = $this->from(route('partner.register.form'))
            ->post(route('partner.register'), [
                'contact_name' => 'Partner Contact',
                'email' => 'partner@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'agency_name' => 'Partner Agency',
                'address' => 'Dakar',
                'opening_hours' => 'Lun-Sam, 9h-19h',
                'whatsapp' => '221770000001',
                'routes' => ['invalid-route-data'],
            ]);

        $response->assertRedirect(route('partner.register.form'));
        $response->assertSessionHasErrors('routes.0');
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('agencies', 0);
    }

    public function test_selected_routes_remain_checked_after_other_validation_errors(): void
    {
        $this->from(route('partner.register.form'))
            ->post(route('partner.register'), [
                'contact_name' => 'Partner Contact',
                'email' => 'partner@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'agency_name' => 'Partner Agency',
                'address' => '',
                'opening_hours' => 'Lun-Sam, 9h-19h',
                'whatsapp' => '221770000001',
                'routes' => [
                    json_encode(['from' => 'SN', 'to' => 'FR']),
                ],
            ])
            ->assertSessionHasErrors('address');

        $this->get(route('partner.register.form'))
            ->assertSee('value="{&quot;from&quot;:&quot;SN&quot;,&quot;to&quot;:&quot;FR&quot;}" checked', false);
    }
}
