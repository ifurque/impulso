<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\MediaUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_profile_and_personalization_from_mi_perfil(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('user.profile'))
            ->assertOk()
            ->assertSee('Mi perfil')
            ->assertSee('Datos personales')
            ->assertSee('Personalización');

        $this->actingAs($user)
            ->get(route('user.customization'))
            ->assertOk()
            ->assertSee('Personalización')
            ->assertSee(route('user.profile'));
    }

    public function test_user_can_update_registered_details_and_profile_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['email_verified_at' => now()]);
        $avatar = UploadedFile::fake()->create('avatar.png', 10, 'image/png');

        $this->actingAs($user)->post(route('user.profile.update'), [
            'name' => 'María Emprendedora',
            'email' => 'maria@example.test',
            'phone' => '1122334455',
            'avatar' => $avatar,
        ])->assertRedirect(route('user.profile'));

        $user->refresh();
        $this->assertSame('María Emprendedora', $user->name);
        $this->assertSame('maria@example.test', $user->email);
        $this->assertSame('1122334455', $user->phone);
        $this->assertNull($user->email_verified_at);
        $this->assertNotNull($user->avatar);
        $this->assertTrue(Storage::disk('public')->exists($user->avatar));
    }

    public function test_discover_places_create_business_action_above_the_search_controls(): void
    {
        $user = User::factory()->create();
        $html = $this->actingAs($user)->get(route('discover'))->assertOk()->getContent();

        $this->assertLessThan(strpos($html, 'id="search-input"'), strpos($html, 'Crear emprendimiento'));
        $this->assertStringContainsString('>Mi perfil</a>', $html);

        Auth::logout();
        $guestHtml = $this->get(route('discover'))->assertOk()->getContent();
        $this->assertLessThan(strpos($guestHtml, 'id="search-input"'), strpos($guestHtml, 'Crear emprendimiento'));
        $this->assertStringContainsString('href="'.route('register').'">Crear emprendimiento', $guestHtml);
    }

    public function test_legacy_upload_paths_use_the_configured_public_filesystem_url(): void
    {
        Storage::fake('public');

        $this->assertSame(
            '/storage/posts/photo.jpg',
            MediaUrl::from('uploads/posts/photo.jpg')
        );
    }

    public function test_local_data_import_requires_explicit_confirmation(): void
    {
        $this->artisan('app:import-local-data')
            ->expectsOutput('Nothing imported. Rerun with --confirm after configuring and backing up the shared services.')
            ->assertExitCode(1);
    }
}