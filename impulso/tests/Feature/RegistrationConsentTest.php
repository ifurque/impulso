<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationConsentTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_requires_both_consents_before_enabling_submission(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Acepto los términos y condiciones')
            ->assertSee('Acepto los términos de privacidad')
            ->assertSee('Acepto todas las condiciones')
            ->assertDontSee('Los textos legales de arriba son ejemplos ficticios')
            ->assertSee('id="register-submit" type="submit" disabled', false);
    }

    public function test_registration_is_rejected_without_both_consents(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Nombre de prueba',
                'email' => 'test@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['terms_accepted', 'privacy_accepted']);

        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_registration_succeeds_when_both_consents_are_accepted(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Nombre de prueba',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms_accepted' => '1',
            'privacy_accepted' => '1',
        ])->assertRedirect(route('user.profile'));

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
        $this->assertAuthenticatedAs(User::where('email', 'test@example.com')->first());
    }
}
