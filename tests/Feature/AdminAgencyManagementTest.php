<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Agency;
use App\Models\Flight;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAgencyManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_the_agency_editor_from_the_agency_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agency = Agency::factory()->create(['name' => 'Agence à modifier']);

        $this->actingAs($admin)
            ->get(route('admin.agencies'))
            ->assertOk()
            ->assertSee(route('admin.agencies.edit', $agency), false)
            ->assertSee('Modifier');

        $this->actingAs($admin)
            ->get(route('admin.agencies.edit', $agency))
            ->assertOk()
            ->assertSee("Modifier l'agence", false)
            ->assertSee('Enregistrer les modifications')
            ->assertSee('name="name"', false);
    }

    public function test_admin_dashboard_displays_pending_agency_actions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agencyUser = User::factory()->create(['role' => 'admin_agence']);
        $agency = Agency::factory()
            ->for($agencyUser, 'user')
            ->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.agencies.approve', $agency), false)
            ->assertSee(route('admin.agencies.reject', $agency), false)
            ->assertSee(route('admin.agencies.edit', $agency), false)
            ->assertSee('Navigation administration');
    }

    public function test_admin_can_open_and_update_their_profile(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('Informations personnelles')
            ->assertSee('Changer le mot de passe')
            ->assertSee(route('admin.agencies'), false);

        $this->actingAs($admin)
            ->patch(route('profile.update'), [
                'name' => 'Admin KONEXA',
                'email' => 'admin-konexa@example.com',
            ])
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin KONEXA',
            'email' => 'admin-konexa@example.com',
            'email_verified_at' => null,
        ]);
    }

    public function test_agency_users_cannot_open_the_admin_profile(): void
    {
        $agencyUser = User::factory()->create(['role' => 'admin_agence']);

        $this->actingAs($agencyUser)
            ->get(route('profile.edit'))
            ->assertForbidden();
    }

    public function test_admin_flights_page_includes_flights_from_soft_deleted_agencies(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agency = Agency::factory()->create(['name' => 'Agence archivée']);
        Flight::factory()->for($agency)->create([
            'flight_date' => now()->addWeek(),
            'drop_off_deadline' => now()->addDays(6),
        ]);
        $agency->delete();

        $this->actingAs($admin)
            ->get(route('admin.flights', ['search' => 'Agence archivée']))
            ->assertOk()
            ->assertSee('Agence archivée');
    }

    public function test_admin_can_update_agency_details_and_sees_confirmation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agency = Agency::factory()->create();

        $response = $this->actingAs($admin)
            ->put(route('admin.agencies.update', $agency), [
                'name' => 'Nouvelle agence',
                'description' => 'Nouvelle description',
                'address' => 'Dakar',
                'opening_hours' => 'Lun-Sam, 9h-19h',
                'whatsapp' => '221770000001',
            ]);

        $response->assertRedirect(route('admin.agencies'))
            ->assertSessionHas('success', 'Agence modifiée avec succès.');

        $this->assertDatabaseHas('agencies', [
            'id' => $agency->id,
            'name' => 'Nouvelle agence',
            'description' => 'Nouvelle description',
            'address' => 'Dakar',
            'opening_hours' => 'Lun-Sam, 9h-19h',
            'whatsapp' => '221770000001',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'update_agency',
            'subject_id' => $agency->id,
        ]);

        $this->get(route('admin.agencies'))
            ->assertOk()
            ->assertSee('Agence modifiée avec succès.');
    }

    public function test_admin_can_approve_a_pending_agency_and_sees_confirmation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agencyUser = User::factory()->create(['role' => 'admin_agence']);
        $agency = Agency::factory()
            ->for($agencyUser, 'user')
            ->create(['status' => 'pending']);

        $response = $this->actingAs($admin)
            ->from(route('admin.agencies'))
            ->post(route('admin.agencies.approve', $agency));

        $response->assertRedirect(route('admin.agencies'))
            ->assertSessionHas('success', 'Agence approuvée avec succès.');

        $this->assertDatabaseHas('agencies', [
            'id' => $agency->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'approve_agency',
            'subject_id' => $agency->id,
        ]);

        $this->get(route('admin.agencies'))
            ->assertOk()
            ->assertSee('Agence approuvée avec succès.');
    }

    public function test_admin_agency_updates_require_valid_details(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agency = Agency::factory()->create(['name' => 'Nom original']);

        $this->actingAs($admin)
            ->put(route('admin.agencies.update', $agency), [
                'name' => '',
                'description' => 'Description',
                'address' => 'Dakar',
                'opening_hours' => 'Lun-Sam',
                'whatsapp' => '221770000001',
            ])
            ->assertSessionHasErrors('name');

        $this->assertDatabaseHas('agencies', [
            'id' => $agency->id,
            'name' => 'Nom original',
        ]);
        $this->assertSame(0, ActivityLog::query()->count());
    }

    public function test_agency_users_cannot_edit_agencies_from_admin(): void
    {
        $agencyUser = User::factory()->create(['role' => 'admin_agence']);
        $agency = Agency::factory()->create();

        $this->actingAs($agencyUser)
            ->get(route('admin.agencies.edit', $agency))
            ->assertForbidden();
    }

    public function test_agency_users_cannot_update_agency_details_from_admin(): void
    {
        $agencyUser = User::factory()->create(['role' => 'admin_agence']);
        $agency = Agency::factory()->create(['name' => 'Nom original']);

        $this->actingAs($agencyUser)
            ->put(route('admin.agencies.update', $agency), [
                'name' => 'Nom modifié',
                'description' => 'Description',
                'address' => 'Dakar',
                'opening_hours' => 'Lun-Sam',
                'whatsapp' => '221770000001',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('agencies', [
            'id' => $agency->id,
            'name' => 'Nom original',
        ]);
    }
}
