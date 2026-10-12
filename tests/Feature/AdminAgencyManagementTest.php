<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Agency;
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
