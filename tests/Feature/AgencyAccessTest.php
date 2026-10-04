<?php

namespace Tests\Feature;

use App\Models\Agency;
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

    public function test_agency_can_update_routes_from_the_route_checkboxes(): void
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
}
