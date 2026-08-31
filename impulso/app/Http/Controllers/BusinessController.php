<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BusinessController extends Controller
{
    public const PUBLIC_PALETTES = [
        'mint' => ['label' => 'Menta', 'accent' => '#176b61', 'soft' => '#e3f3e9', 'ink' => '#18352e', 'paper' => '#f6faf6', 'line' => '#d3e5da'],
        'sun' => ['label' => 'Sol', 'accent' => '#9a4d12', 'soft' => '#fff0cf', 'ink' => '#3b2517', 'paper' => '#fffaf1', 'line' => '#ead9b9'],
        'coral' => ['label' => 'Coral', 'accent' => '#a5384f', 'soft' => '#ffe7e5', 'ink' => '#3d2027', 'paper' => '#fff8f7', 'line' => '#efd4d4'],
        'ocean' => ['label' => 'Océano', 'accent' => '#176889', 'soft' => '#e0f1f6', 'ink' => '#17313d', 'paper' => '#f5fbfc', 'line' => '#d1e4ea'],
    ];

    public const PUBLIC_BACKGROUNDS = [
        'plain' => 'Liso',
        'grid' => 'Cuadrícula suave',
        'dots' => 'Puntos suaves',
        'paper' => 'Papel cálido',
    ];

    public function customization(Business $business, Request $request)
    {
        abort_unless($business->canBeManagedBy($request->user()), 403);
        return view('business.customization', ['business' => $business, 'palettes' => self::PUBLIC_PALETTES, 'backgrounds' => self::PUBLIC_BACKGROUNDS]);
    }

    public function updateCustomization(Request $request, Business $business)
    {
        abort_unless($business->canBeManagedBy($request->user()), 403);
        $data = $request->validate(['public_palette' => ['required', 'in:mint,sun,coral,ocean'], 'public_background' => ['required', 'in:plain,grid,dots,paper']]);
        $business->update($data);
        return redirect()->route('business.customization', $business)->with('success', 'Personalización guardada.');
    }

    public function panels(Request $request)
    {
        return view('business.panels', [
            'businesses' => Business::query()
                ->where('owner_id', $request->user()->id)
                ->orWhereHas('members', fn ($query) => $query->whereKey($request->user()->id)->wherePivotIn('role', ['owner', 'administrator']))
                ->latest('created_at')
                ->withCount(['appointments as pending_appointments_count' => fn ($query) => $query->whereIn('status', ['pending', 'confirmed'])])
                ->get(),
        ]);
    }

    public function create() { return view('business.create'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:1000'],
            'location' => ['required', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:180'],
            'appointments_enabled' => ['boolean'],
            'delivery_enabled' => ['boolean'],
            'delivery_radius_km' => ['nullable', 'integer', 'min:0', 'max:50'],
            'delivery_cost' => ['nullable', 'integer', 'min:0'],
            'customer_payment_methods' => ['nullable', 'array'],
            'customer_payment_methods.*' => ['string', 'in:efectivo,transferencia,tarjeta,mercado_pago,qr'],
            'business_payment_methods' => ['nullable', 'array'],
            'business_payment_methods.*' => ['string', 'in:transferencia,mercado_pago,efectivo,tarjeta,qr'],
            'profile_photo' => ['nullable', 'image', 'max:5120'],
            'cover_photo' => ['nullable', 'image', 'max:8192'],
        ]);
        foreach (['profile_photo', 'cover_photo'] as $image) {
            if ($request->hasFile($image)) {
                $data[$image] = $request->file($image)->store('businesses', 'public');
            }
        }
        $business = $request->user()->ownedBusinesses()->create($data + [
            'appointments_enabled' => $request->boolean('appointments_enabled'),
            'delivery_enabled' => $request->boolean('delivery_enabled'),
            'delivery_radius_km' => $request->input('delivery_radius_km', 0),
            'delivery_cost' => $request->input('delivery_cost', 0),
            'payment_methods_customer' => $request->input('customer_payment_methods', []),
            'payment_methods_business' => $request->input('business_payment_methods', []),
            'appointment_slot_duration' => 30,
            'slug' => Str::slug($data['name']).'-'.Str::random(5),
        ]);
        $business->members()->attach($request->user()->id, ['role' => 'owner', 'joined_at' => now()]);
        if ($business->appointments_enabled) {
            foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
                $business->availabilityHours()->create([
                    'day_of_week' => $day,
                    'opening_time' => '09:00',
                    'closing_time' => '18:00',
                    'is_closed' => false,
                ]);
            }
        }
        $request->user()->update(['is_entrepreneur' => true]);
        ExpenseCategory::query()->insertOrIgnore([
            ['name' => 'Materia prima', 'slug' => 'materia-prima', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sueldos', 'slug' => 'sueldos', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bienes y equipamiento', 'slug' => 'bienes-equipamiento', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Servicios', 'slug' => 'servicios', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Alquiler', 'slug' => 'alquiler', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Otros', 'slug' => 'otros', 'created_at' => now(), 'updated_at' => now()],
        ]);
        return redirect()->route('dashboard', $business)->with('success', 'Tu emprendimiento fue creado.');
    }

    public function show(Business $business)
    {
        abort_unless($business->is_public, 404);
        return view('business.show', ['business' => $business->load(['owner', 'products', 'socialLinks', 'reviews' => fn ($query) => $query->where('is_visible', true)->latest(), 'posts' => fn ($query) => $query->where('is_published', true)->where(fn ($query) => $query->whereNull('starts_at')->orWhereDate('starts_at', '<=', today()))->where(fn ($query) => $query->whereNull('ends_at')->orWhereDate('ends_at', '>=', today()))]), 'palettes' => self::PUBLIC_PALETTES]);
    }
}