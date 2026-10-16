<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\AgencyRoute;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgencyAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_agency_policy_restricts_access()
    {
        $agency1 = Agency::factory()->create(['status' => 'approved']);
        $agency2 = Agency::factory()->create(['status' => 'approved']);

        $agency1User = User::factory()->create(['role' => 'admin_agence']);
        $agency1->user()->associate($agency1User)->save();

        $flight1 = Flight::factory()->create(['agency_id' => $agency1->id]);
        $flight2 = Flight::factory()->create(['agency_id' => $agency2->id]);

        $this->assertTrue($agency1User->can('update', $flight1));
        $this->assertFalse($agency1User->can('update', $flight2));
    }

    public function test_admin_policy_allows_all_access()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agency = Agency::factory()->create(['status' => 'approved']);

        $this->assertTrue($admin->can('delete', $agency));
    }

    public function test_agence_user_cannot_delete_agency()
    {
        $agency = Agency::factory()->create(['status' => 'approved']);
        $agencyUser = User::factory()->create(['role' => 'admin_agence']);
        $agency->user()->associate($agencyUser)->save();

        $this->assertFalse($agencyUser->can('delete', $agency));
    }

    public function test_only_approved_agencies_are_publicly_visible()
    {
        $approvedAgency = Agency::factory()->create(['status' => 'approved']);
        $pendingAgency = Agency::factory()->create(['status' => 'pending']);

        $publicAgencies = Agency::approved()->get();

        $this->assertTrue($publicAgencies->contains($approvedAgency));
        $this->assertFalse($publicAgencies->contains($pendingAgency));
    }

    public function test_unverified_agency_users_are_redirected_from_their_dashboard(): void
    {
        $agencyUser = User::factory()->unverified()->create(['role' => 'admin_agence']);

        $this->actingAs($agencyUser)
            ->get(route('agency.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_agency_can_update_routes_from_encoded_route_data(): void
    {
        $agencyUser = User::factory()->create(['role' => 'admin_agence']);
        $agency = Agency::factory()->for($agencyUser, 'user')->create();

        $response = $this->actingAs($agencyUser)->post(route('agency.routes.update'), [
            'routes' => [
                json_encode(['from' => 'SN', 'to' => 'FR']),
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('agency_routes', [
            'agency_id' => $agency->id,
            'from_country' => 'SN',
            'to_country' => 'FR',
        ]);
    }

    public function test_agency_can_save_routes_for_countries_not_in_the_original_list(): void
    {
        $agencyUser = User::factory()->create(['role' => 'admin_agence']);
        $agency = Agency::factory()->for($agencyUser, 'user')->create();

        $response = $this->actingAs($agencyUser)->post(route('agency.routes.update'), [
            'routes' => [
                ['from' => 'Chine', 'to' => "Côte d'Ivoire"],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('agency_routes', [
            'agency_id' => $agency->id,
            'from_country' => 'CN',
            'to_country' => 'CI',
        ]);
    }

    public function test_public_agency_search_accepts_international_country_names(): void
    {
        $agency = Agency::factory()->create(['name' => 'International Parcel Agency']);
        AgencyRoute::factory()->for($agency)->create([
            'from_country' => 'CN',
            'to_country' => 'SN',
        ]);

        $this->get(route('agences', ['from' => 'Chine', 'to' => 'Sénégal']))
            ->assertOk()
            ->assertSee('International Parcel Agency')
            ->assertSee('Chine → Sénégal');
    }

    public function test_agency_can_create_a_flight_for_countries_not_in_the_original_list(): void
    {
        $agencyUser = User::factory()->create(['role' => 'admin_agence']);
        $agency = Agency::factory()->for($agencyUser, 'user')->create();

        $this->actingAs($agencyUser)
            ->post(route('agency.flights.store'), [
                'from_country' => 'Chine',
                'to_country' => 'Sénégal',
                'flight_date' => now()->addWeek()->toDateString(),
                'airline' => 'International Air',
                'total_kg' => 100,
                'remaining_kg' => 90,
                'price_per_kg' => 4.5,
                'currency' => 'USD',
            ])
            ->assertRedirect(route('agency.flights'));

        $this->assertDatabaseHas('flights', [
            'agency_id' => $agency->id,
            'from_country' => 'CN',
            'to_country' => 'SN',
            'currency' => 'USD',
        ]);
    }

    public function test_agency_flight_form_renders_all_fields_with_its_styling_hooks(): void
    {
        $agencyUser = User::factory()->create(['role' => 'admin_agence']);
        Agency::factory()->for($agencyUser, 'user')->create();

        $this->actingAs($agencyUser)
            ->get(route('agency.flights.create'))
            ->assertOk()
            ->assertSee('class="table-container flight-form-card"', false)
            ->assertSee('class="auth-form flight-form"', false)
            ->assertSee('Pays de départ')
            ->assertSee('Prix par kg')
            ->assertSee('Kilos restants')
            ->assertSee('Créer le vol')
            ->assertSee('class="flight-form-hint"', false);
    }

    public function test_agency_profile_and_routes_pages_render_their_styled_forms(): void
    {
        $agencyUser = User::factory()->create(['role' => 'admin_agence']);
        $agency = Agency::factory()->for($agencyUser, 'user')->create();
        $agency->routes()->create([
            'from_country' => 'SN',
            'to_country' => 'FR',
        ]);

        $this->actingAs($agencyUser)
            ->get(route('agency.profile'))
            ->assertOk()
            ->assertSee('class="agency-settings-card"', false)
            ->assertSee('class="agency-settings-form"', false)
            ->assertSee('Modifier mon profil')
            ->assertSee(asset('css/dashboard.css'));

        $this->get(route('agency.routes'))
            ->assertOk()
            ->assertSee('class="agency-settings-card agency-routes-card"', false)
            ->assertSee('class="route-editor__row"', false)
            ->assertSee('name="routes[0][from]"', false)
            ->assertSee('name="routes[0][to]"', false)
            ->assertSee('Enregistrer les trajets');
    }
}
