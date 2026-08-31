<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->boolean('delivery_enabled')->default(false)->after('appointments_enabled');
            $table->unsignedSmallInteger('delivery_radius_km')->default(0)->after('delivery_enabled');
            $table->unsignedInteger('delivery_cost')->default(0)->after('delivery_radius_km');
            $table->json('payment_methods_customer')->nullable()->after('delivery_cost');
            $table->json('payment_methods_business')->nullable()->after('payment_methods_customer');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_enabled',
                'delivery_radius_km',
                'delivery_cost',
                'payment_methods_customer',
                'payment_methods_business',
            ]);
        });
    }
};
