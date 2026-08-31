<?php

namespace Tests\Feature;

use App\Mail\AppointmentConfirmation;
use App\Mail\OrderReceived;
use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BusinessInteractionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_request_and_confirm_an_appointment_by_email(): void
    {
        Mail::fake();
        $business = Business::create(['owner_id' => User::factory()->create()->id, 'name' => 'Taller Norte', 'category' => 'Oficios', 'description' => 'Un taller', 'location' => 'Centro']);

        $response = $this->post(route('appointments.store', $business), [
            'guest_name' => 'Ana Pérez', 'guest_email' => 'ana@example.com', 'appointment_date' => now()->addDay()->format('Y-m-d'), 'start_time' => '10:00',
        ]);

        $appointment = $business->appointments()->first();
        $response->assertSessionHas('success');
        $this->assertSame('awaiting_confirmation', $appointment->status);
        Mail::assertSent(AppointmentConfirmation::class, fn ($mail) => $mail->hasTo('ana@example.com'));
        $this->get(route('appointments.confirm', $appointment->confirmation_token))->assertRedirect();
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id, 'status' => 'pending', 'confirmation_token' => null]);
    }

    public function test_anyone_can_leave_a_five_star_review(): void
    {
        $business = Business::create(['owner_id' => User::factory()->create()->id, 'name' => 'Cocina Sur', 'category' => 'Gastronomía', 'description' => 'Comida', 'location' => 'Barrio']);

        $this->post(route('reviews.store', $business), ['reviewer_name' => 'Luis', 'rating' => 5, 'body' => 'Excelente atención.'])->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', ['business_id' => $business->id, 'rating' => 5, 'body' => 'Excelente atención.']);
    }

    public function test_authenticated_user_can_open_a_public_business_profile(): void
    {
        $owner = User::factory()->create();
        $visitor = User::factory()->create();
        $business = Business::create(['owner_id' => $owner->id, 'name' => 'Estudio Centro', 'category' => 'Servicios', 'description' => 'Atención', 'location' => 'Centro']);

        $this->actingAs($visitor)->get(route('business.show', $business))->assertOk();
    }

    public function test_owner_with_direct_business_access_sees_the_panel_link_and_can_open_panel(): void
    {
        $owner = User::factory()->create();
        $business = $owner->ownedBusinesses()->create([
            'name' => 'Negocio directo',
            'category' => 'Servicios',
            'description' => 'Sin pivot de miembro',
            'location' => 'Centro',
        ]);

        $this->actingAs($owner)
            ->get(route('home'))
            ->assertSee('Mis emprendimientos');

        $this->actingAs($owner)
            ->get(route('business.panels'))
            ->assertOk()
            ->assertSee($business->name);
    }

    public function test_owner_can_open_every_destination_linked_from_the_business_panel(): void
    {
        $owner = User::factory()->create();
        $business = $owner->ownedBusinesses()->create([
            'name' => 'Panel completo',
            'category' => 'Servicios',
            'description' => 'Prueba de navegación',
            'location' => 'Centro',
            'appointments_enabled' => true,
        ]);

        foreach ([
            route('dashboard', $business),
            route('management.index', $business),
            route('movements.index', $business),
            route('expenses.index', $business),
            route('posts.index', $business),
            route('availability.index', $business),
            route('business.customization', $business),
            route('business.show', $business),
        ] as $url) {
            $this->actingAs($owner)->get($url)->assertOk();
        }
    }

    public function test_management_panel_displays_guest_appointments_without_an_error(): void
    {
        $owner = User::factory()->create();
        $business = $owner->ownedBusinesses()->create([
            'name' => 'Turnos invitados',
            'category' => 'Servicios',
            'description' => 'Prueba de turno',
            'location' => 'Centro',
        ]);
        $business->appointments()->create([
            'guest_name' => 'Cliente invitado',
            'guest_email' => 'cliente@impulso.local',
            'appointment_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '10:30',
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->get(route('management.index', $business))
            ->assertOk()
            ->assertSee('Cliente invitado');
    }

    public function test_business_can_configure_delivery_and_payment_methods(): void
    {
        $owner = User::factory()->create();
        $business = Business::create([
            'owner_id' => $owner->id,
            'name' => 'Panadería Luna',
            'category' => 'Gastronomía',
            'description' => 'Pan artesanal',
            'location' => 'Almagro',
            'delivery_enabled' => true,
            'delivery_radius_km' => 8,
            'delivery_cost' => 600,
            'payment_methods_customer' => ['efectivo', 'transferencia', 'tarjeta'],
            'payment_methods_business' => ['transferencia', 'mercado_pago'],
        ]);

        $this->assertTrue($business->delivery_enabled);
        $this->assertSame(8, $business->delivery_radius_km);
        $this->assertSame(600, $business->delivery_cost);
        $this->assertSame(['efectivo', 'transferencia', 'tarjeta'], $business->payment_methods_customer);
        $this->assertSame(['transferencia', 'mercado_pago'], $business->payment_methods_business);
    }

    public function test_client_can_place_a_delivery_order_with_cash_payment(): void
    {
        $owner = User::factory()->create();
        $business = Business::create([
            'owner_id' => $owner->id,
            'name' => 'Café del Sur',
            'category' => 'Gastronomía',
            'description' => 'Café y brunch',
            'location' => 'Villa Urquiza',
            'delivery_enabled' => true,
            'delivery_radius_km' => 10,
            'delivery_cost' => 700,
            'payment_methods_customer' => ['efectivo', 'qr', 'transferencia'],
        ]);

        $product = $business->products()->create([
            'name' => 'Brunch completo',
            'description' => 'Desayuno completo',
            'type' => 'product',
            'price' => 2100,
            'is_active' => true,
        ]);

        $this->post(route('orders.store', $business), [
            'product_id' => $product->id,
            'quantity' => 1,
            'customer_name' => 'Agustina',
            'customer_phone' => '1122334455',
            'delivery_address' => 'Av. Siempre Viva 123',
            'delivery_notes' => 'Portón negro',
            'payment_method' => 'efectivo',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'business_id' => $business->id,
            'product_id' => $product->id,
            'status' => 'pending',
            'payment_method' => 'efectivo',
            'delivery_cost' => 700,
            'subtotal' => 2100,
            'total' => 2800,
        ]);
    }

    public function test_owner_can_see_recent_delivery_orders_in_management_panel(): void
    {
        $owner = User::factory()->create();
        $business = Business::create([
            'owner_id' => $owner->id,
            'name' => 'Café del Sur',
            'category' => 'Gastronomía',
            'description' => 'Café y brunch',
            'location' => 'Villa Urquiza',
            'delivery_enabled' => true,
            'delivery_radius_km' => 10,
            'delivery_cost' => 700,
            'payment_methods_customer' => ['efectivo', 'qr', 'transferencia'],
        ]);

        $product = $business->products()->create([
            'name' => 'Brunch completo',
            'description' => 'Desayuno completo',
            'type' => 'product',
            'price' => 2100,
            'is_active' => true,
        ]);

        $business->orders()->create([
            'product_id' => $product->id,
            'customer_name' => 'Agustina',
            'customer_phone' => '1122334455',
            'delivery_address' => 'Av. Siempre Viva 123',
            'delivery_notes' => 'Portón negro',
            'quantity' => 1,
            'subtotal' => 2100,
            'delivery_cost' => 700,
            'total' => 2800,
            'payment_method' => 'efectivo',
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->get(route('management.index', $business))
            ->assertOk()
            ->assertSee('Pedidos con envío')
            ->assertSee('Agustina');
    }

    public function test_owner_can_update_order_status_from_the_panel(): void
    {
        $owner = User::factory()->create();
        $business = Business::create([
            'owner_id' => $owner->id,
            'name' => 'Café del Sur',
            'category' => 'Gastronomía',
            'description' => 'Café y brunch',
            'location' => 'Villa Urquiza',
            'delivery_enabled' => true,
            'delivery_radius_km' => 10,
            'delivery_cost' => 700,
            'payment_methods_customer' => ['efectivo', 'qr', 'transferencia'],
        ]);

        $product = $business->products()->create([
            'name' => 'Brunch completo',
            'description' => 'Desayuno completo',
            'type' => 'product',
            'price' => 2100,
            'is_active' => true,
        ]);

        $order = $business->orders()->create([
            'product_id' => $product->id,
            'customer_name' => 'Agustina',
            'customer_phone' => '1122334455',
            'delivery_address' => 'Av. Siempre Viva 123',
            'delivery_notes' => 'Portón negro',
            'quantity' => 1,
            'subtotal' => 2100,
            'delivery_cost' => 700,
            'total' => 2800,
            'payment_method' => 'efectivo',
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->post(route('orders.status', [$business, $order]), ['status' => 'confirmed'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'confirmed']);
    }

    public function test_owner_sees_pending_delivery_orders_notification_in_dashboard(): void
    {
        $owner = User::factory()->create();
        $business = Business::create([
            'owner_id' => $owner->id,
            'name' => 'Café del Sur',
            'category' => 'Gastronomía',
            'description' => 'Café y brunch',
            'location' => 'Villa Urquiza',
            'delivery_enabled' => true,
            'delivery_radius_km' => 10,
            'delivery_cost' => 700,
            'payment_methods_customer' => ['efectivo', 'qr', 'transferencia'],
        ]);

        $product = $business->products()->create([
            'name' => 'Brunch completo',
            'description' => 'Desayuno completo',
            'type' => 'product',
            'price' => 2100,
            'is_active' => true,
        ]);

        $business->orders()->create([
            'product_id' => $product->id,
            'customer_name' => 'Agustina',
            'customer_phone' => '1122334455',
            'delivery_address' => 'Av. Siempre Viva 123',
            'delivery_notes' => 'Portón negro',
            'quantity' => 1,
            'subtotal' => 2100,
            'delivery_cost' => 700,
            'total' => 2800,
            'payment_method' => 'efectivo',
            'status' => 'pending',
        ]);

        $this->actingAs($owner)
            ->get(route('dashboard', $business))
            ->assertOk()
            ->assertSee('Pedidos pendientes')
            ->assertSee('1');
    }

    public function test_order_submission_sends_notification_email_to_business_owner(): void
    {
        Mail::fake();

        $owner = User::factory()->create(['email' => 'dueno@impulso.local']);
        $business = Business::create([
            'owner_id' => $owner->id,
            'name' => 'Café del Sur',
            'category' => 'Gastronomía',
            'description' => 'Café y brunch',
            'location' => 'Villa Urquiza',
            'delivery_enabled' => true,
            'delivery_radius_km' => 10,
            'delivery_cost' => 700,
            'payment_methods_customer' => ['efectivo', 'qr', 'transferencia'],
        ]);

        $product = $business->products()->create([
            'name' => 'Brunch completo',
            'description' => 'Desayuno completo',
            'type' => 'product',
            'price' => 2100,
            'is_active' => true,
        ]);

        $this->post(route('orders.store', $business), [
            'product_id' => $product->id,
            'quantity' => 1,
            'customer_name' => 'Agustina',
            'customer_phone' => '1122334455',
            'delivery_address' => 'Av. Siempre Viva 123',
            'delivery_notes' => 'Portón negro',
            'payment_method' => 'efectivo',
        ])->assertSessionHas('success');

        Mail::assertSent(OrderReceived::class, fn ($mail) => $mail->hasTo('dueno@impulso.local'));
    }
}