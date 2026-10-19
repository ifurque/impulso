<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Business;
use App\Support\MediaUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
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

    public function test_business_administrator_can_open_profile_without_pivot_query_error(): void
    {
        $owner = User::factory()->create();
        $administrator = User::factory()->create();
        $business = Business::create([
            'owner_id' => $owner->id,
            'name' => 'Taller Norte',
        ]);
        $business->members()->attach($administrator->id, [
            'role' => 'administrator',
            'joined_at' => now(),
        ]);

        $this->actingAs($administrator)
            ->get(route('user.profile'))
            ->assertOk()
            ->assertSee('Mi perfil');
    }

    public function test_user_can_update_registered_details_and_profile_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['email_verified_at' => now()]);
        $avatar = UploadedFile::fake()->create('avatar.png', 10, 'image/png');

        $this->actingAs($user)->post(route('user.profile.update'), [
            'first_name' => 'María',
            'last_name' => 'Emprendedora',
            'document_number' => '30.123.456',
            'address' => 'Calle Falsa 123',
            'postal_code' => 'C1000',
            'city' => 'Buenos Aires',
            'province' => 'Buenos Aires',
            'email' => 'maria@example.test',
            'phone' => '1122334455',
            'avatar' => $avatar,
        ])->assertRedirect(route('user.profile'));

        $user->refresh();
        $this->assertSame('María Emprendedora', $user->name);
        $this->assertSame('María', $user->first_name);
        $this->assertSame('30123456', $user->document_number);
        $this->assertSame('Buenos Aires', $user->province);
        $this->assertSame('maria@example.test', $user->email);
        $this->assertSame('1122334455', $user->phone);
        $this->assertNull($user->email_verified_at);
        $this->assertNotNull($user->avatar);
        $this->assertTrue(Storage::disk('public')->exists($user->avatar));
    }

    public function test_user_can_save_profile_changes_with_name_and_email_fields_empty(): void
    {
        $user = User::factory()->create([
            'name' => 'Ana Gómez',
            'first_name' => 'Ana',
            'last_name' => 'Gómez',
            'email' => 'ana@example.test',
        ]);

        $this->actingAs($user)->post(route('user.profile.update'), [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'city' => 'Rosario',
        ])->assertRedirect(route('user.profile'));

        $user->refresh();
        $this->assertSame('Ana Gómez', $user->name);
        $this->assertSame('Ana', $user->first_name);
        $this->assertSame('Gómez', $user->last_name);
        $this->assertSame('ana@example.test', $user->email);
        $this->assertSame('Rosario', $user->city);
    }

    public function test_document_number_must_be_unique(): void
    {
        User::factory()->create(['document_number' => '30123456']);
        $user = User::factory()->create();

        $this->actingAs($user)->from(route('user.profile'))->post(route('user.profile.update'), [
            'first_name' => 'Ana',
            'last_name' => 'Gómez',
            'document_number' => '30123456',
            'email' => $user->email,
        ])->assertSessionHasErrors('document_number');
    }

    public function test_user_cannot_verify_email_or_phone_without_the_signed_link(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create(['phone' => '1122334455']);

        $this->actingAs($user)->post(route('verification.send'))->assertRedirect();
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->actingAs($user)->post(route('phone.verification.send'))->assertRedirect();
        $this->assertNull($user->fresh()->phone_verified_at);

        $this->actingAs($user)->post(route('user.profile.update'), [
            'first_name' => 'Ana', 'last_name' => 'Gómez', 'email' => $user->email,
            'phone' => '1122334455', 'phone_verified_at' => now()->toDateTimeString(),
            'email_verified_at' => now()->toDateTimeString(),
        ]);
        $this->assertNull($user->fresh()->phone_verified_at);
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_phone_signed_link_verifies_and_rejects_other_users_and_changes(): void
    {
        $user = User::factory()->create(['phone' => '1122334455']);
        $other = User::factory()->create();
        $url = URL::temporarySignedRoute('phone.verification.verify', now()->addHour(), [
            'id' => $user->id, 'hash' => sha1('1122334455'),
        ]);

        $this->actingAs($other)->get($url)->assertForbidden();
        $this->actingAs($user)->get($url)->assertRedirect(route('user.profile'));
        $this->assertNotNull($user->fresh()->phone_verified_at);

        $this->actingAs($user)->post(route('user.profile.update'), [
            'first_name' => 'Ana', 'last_name' => 'Gómez', 'email' => $user->email, 'phone' => '999888777',
        ]);
        $this->assertNull($user->fresh()->phone_verified_at);
    }

    public function test_profile_form_does_not_update_password_even_if_browser_sends_autofilled_password_fields(): void
    {
        $user = User::factory()->create(['password' => 'existing-password']);

        $this->actingAs($user)->post(route('user.profile.update'), [
            'first_name' => 'Leandro',
            'last_name' => 'Sacco',
            'email' => $user->email,
            'password' => 'autofilled-password',
            'password_confirmation' => 'autofilled-password',
        ])->assertRedirect(route('user.profile'));

        $this->assertTrue(password_verify('existing-password', $user->refresh()->password));
    }

    public function test_profile_page_has_one_save_button_and_no_password_prompt(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('user.profile'))
            ->assertOk()
            ->assertSee('Guardar cambios')
            ->assertSee('data-help-target="profile-save"', false)
            ->assertDontSee('Actualizar contraseña')
            ->assertDontSee('name="current_password"')
            ->assertDontSee('name="password"');
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