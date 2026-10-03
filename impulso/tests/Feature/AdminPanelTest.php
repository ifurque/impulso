<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_superadmins_can_access_the_admin_panel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.users'))->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'superadmin']))
            ->get(route('admin.users'))->assertOk()->assertSee('Usuarios');
    }

    public function test_superadmin_can_grant_admin_and_impulso_plus(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin']);
        $user = User::factory()->create();
        $business = Business::create([
            'owner_id' => $user->id,
            'name' => 'Tienda de prueba',
            'slug' => 'tienda-prueba',
            'location' => 'Córdoba',
            'category' => 'Alimentos',
        ]);

        $this->actingAs($admin)->get(route('admin.businesses'))->assertOk()->assertSee('Tienda de prueba')->assertSee('Córdoba')->assertSee('Alimentos');
        $this->actingAs($admin)->patch(route('admin.users.admin', $user))->assertRedirect();
        $this->assertSame('superadmin', $user->fresh()->role);

        $this->patch(route('admin.businesses.plus', $business))->assertRedirect();
        $this->assertTrue($business->fresh()->is_plus);
        $this->get(route('discover'))->assertOk()->assertSee('Verificado por +Impulso');
    }

    public function test_superadmin_can_edit_and_delete_business_and_user(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin']);
        $user = User::factory()->create();
        $business = Business::create(['owner_id' => $user->id, 'name' => 'Anterior', 'slug' => 'anterior']);

        $this->actingAs($admin)->get(route('admin.businesses.edit', $business))->assertOk()->assertSee('Modificar emprendimiento');
        $this->get(route('admin.users.edit', $user))->assertOk()->assertSee('Modificar usuario');
        $this->actingAs($admin)->put(route('admin.businesses.update', $business), [
            'owner_id' => $user->id,
            'name' => 'Actualizado',
            'is_public' => '1',
        ])->assertRedirect(route('admin.businesses'));
        $this->assertSame('Actualizado', $business->fresh()->name);

        $this->delete(route('admin.businesses.delete', $business))->assertRedirect();
        $this->assertDatabaseMissing('businesses', ['id' => $business->id]);

        $this->delete(route('admin.users.delete', $user))->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}