<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\AgencyRoute;
use App\Models\PartnerApplication;
use App\Models\User;
use App\Notifications\NewPartnerNotification;
use App\Notifications\PartnerEmailVerificationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PartnerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_partner_registration_form_can_be_rendered(): void
    {
        $this->get(route('partner.register.form'))
            ->assertOk()
            ->assertSee('Devenir partenaire')
            ->assertSee('name="routes[0][from]"', false)
            ->assertSee('name="routes[0][to]"', false)
            ->assertDontSee('<footer', false);
    }

    public function test_partner_registration_sends_verification_email_without_creating_partner_records(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Notification::fake();

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

        $response->assertRedirect(route('partner.verification.pending'));
        $response->assertSessionHas('status');

        $application = PartnerApplication::where('email', 'partner@example.com')->firstOrFail();
        $this->assertModelExists($application);
        $this->assertTrue(Hash::check('password123', $application->password));
        $this->assertDatabaseMissing('users', ['email' => 'partner@example.com']);
        $this->assertDatabaseCount('agencies', 0);
        $this->assertDatabaseCount('agency_routes', 0);

        $verificationUrl = null;
        Notification::assertSentOnDemand(
            PartnerEmailVerificationNotification::class,
            function (
                PartnerEmailVerificationNotification $notification,
                array $channels,
                object $notifiable,
            ) use (&$verificationUrl): bool {
                $verificationUrl = $notification->verificationUrl;

                return $notifiable->routes['mail'] === 'partner@example.com';
            },
        );

        $this->assertNotNull($verificationUrl);
        Notification::assertNotSentTo($admin, NewPartnerNotification::class);
    }

    public function test_partner_can_request_a_new_email_verification_link(): void
    {
        Notification::fake();

        $this->post(route('partner.register'), [
            'contact_name' => 'Partner Contact',
            'email' => 'partner@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agency_name' => 'Partner Agency',
            'address' => 'Dakar',
            'opening_hours' => 'Lun-Sam, 9h-19h',
            'whatsapp' => '221770000001',
            'routes' => [
                json_encode(['from' => 'SN', 'to' => 'FR']),
            ],
        ])->assertRedirect(route('partner.verification.pending'));

        $this->get(route('partner.verification.pending'))
            ->assertOk()
            ->assertSee('Votre compte partenaire n’est pas encore créé.')
            ->assertDontSee('<footer', false);

        $this->post(route('partner.verification.resend'), ['email' => 'PARTNER@example.com'])
            ->assertRedirect(route('partner.verification.pending'))
            ->assertSessionHas('status');

        Notification::assertSentOnDemandTimes(PartnerEmailVerificationNotification::class, 2);
        $verificationUrls = Notification::sent(
            new AnonymousNotifiable,
            PartnerEmailVerificationNotification::class,
        )->pluck('verificationUrl')->all();
        $this->assertCount(2, $verificationUrls);
        $this->get($verificationUrls[0])->assertForbidden();
        $this->get($verificationUrls[1])->assertOk();
        $this->assertDatabaseMissing('users', ['email' => 'partner@example.com']);
    }

    public function test_confirming_email_creates_verified_partner_and_notifies_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Notification::fake();

        $this->post(route('partner.register'), [
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
        ])->assertRedirect(route('partner.verification.pending'));

        $verificationUrl = null;
        Notification::assertSentOnDemand(
            PartnerEmailVerificationNotification::class,
            function (
                PartnerEmailVerificationNotification $notification,
                array $channels,
                object $notifiable,
            ) use (&$verificationUrl): bool {
                $verificationUrl = $notification->verificationUrl;

                return $notifiable->routes['mail'] === 'partner@example.com';
            },
        );

        $this->assertNotNull($verificationUrl);
        $this->get($verificationUrl)
            ->assertOk()
            ->assertSee('Confirmer votre adresse e-mail')
            ->assertDontSee('<footer', false);

        $this->post($verificationUrl)
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $user = User::where('email', 'partner@example.com')->firstOrFail();
        $agency = Agency::where('user_id', $user->id)->firstOrFail();
        $agencyRoute = AgencyRoute::where('agency_id', $agency->id)->firstOrFail();

        $this->assertModelExists($user);
        $this->assertModelExists($agency);
        $this->assertModelExists($agencyRoute);
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertSame('admin_agence', $user->role);
        $this->assertSame('pending', $agency->status);
        $this->assertSame('SN', $agencyRoute->from_country);
        $this->assertSame('FR', $agencyRoute->to_country);
        $this->assertDatabaseMissing('partner_applications', ['email' => 'partner@example.com']);

        Notification::assertSentTo($admin, NewPartnerNotification::class);
    }

    public function test_invalid_partner_verification_token_does_not_create_records(): void
    {
        Notification::fake();

        $this->post(route('partner.register'), [
            'contact_name' => 'Partner Contact',
            'email' => 'partner@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agency_name' => 'Partner Agency',
            'address' => 'Dakar',
            'opening_hours' => 'Lun-Sam, 9h-19h',
            'whatsapp' => '221770000001',
            'routes' => [
                json_encode(['from' => 'SN', 'to' => 'FR']),
            ],
        ])->assertRedirect(route('partner.verification.pending'));

        $application = PartnerApplication::where('email', 'partner@example.com')->firstOrFail();
        $invalidUrl = URL::temporarySignedRoute(
            'partner.verify',
            now()->addHour(),
            ['partnerApplication' => $application->id, 'token' => 'invalid-token'],
        );

        $this->get($invalidUrl)->assertForbidden();

        $this->assertModelExists($application);
        $this->assertDatabaseMissing('users', ['email' => 'partner@example.com']);
        $this->assertDatabaseCount('agencies', 0);
    }

    public function test_expired_partner_verification_link_does_not_create_records(): void
    {
        Notification::fake();

        $this->post(route('partner.register'), [
            'contact_name' => 'Partner Contact',
            'email' => 'partner@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agency_name' => 'Partner Agency',
            'address' => 'Dakar',
            'opening_hours' => 'Lun-Sam, 9h-19h',
            'whatsapp' => '221770000001',
            'routes' => [
                json_encode(['from' => 'SN', 'to' => 'FR']),
            ],
        ])->assertRedirect(route('partner.verification.pending'));

        $application = PartnerApplication::where('email', 'partner@example.com')->firstOrFail();
        $this->travelTo(now()->addDays(2));
        $expiredUrl = URL::temporarySignedRoute(
            'partner.verify',
            now()->subMinute(),
            ['partnerApplication' => $application->id, 'token' => 'invalid-token'],
        );

        $this->get($expiredUrl)->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'partner@example.com']);
        $this->assertDatabaseCount('agencies', 0);
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

    public function test_entered_routes_remain_available_after_other_validation_errors(): void
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
            ->assertSee('name="routes[0][from]" value="Sénégal"', false)
            ->assertSee('name="routes[0][to]" value="France"', false);
    }

    public function test_partner_can_register_with_countries_outside_the_original_list(): void
    {
        Notification::fake();

        $this->post(route('partner.register'), [
            'contact_name' => 'Partner Contact',
            'email' => 'international@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'agency_name' => 'International Agency',
            'address' => 'Shanghai',
            'opening_hours' => 'Every day',
            'whatsapp' => '8613800000000',
            'routes' => [
                ['from' => 'Chine', 'to' => "Côte d'Ivoire"],
            ],
        ])->assertRedirect(route('partner.verification.pending'));

        $application = PartnerApplication::where('email', 'international@example.com')->firstOrFail();

        $this->assertSame([
            ['from' => 'CN', 'to' => 'CI'],
        ], $application->routes);
    }
}
