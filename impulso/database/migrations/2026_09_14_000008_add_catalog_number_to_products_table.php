<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('catalog_number')->nullable()->after('business_id');
            $table->unique(['business_id', 'catalog_number']);
        });

        DB::table('products')
            ->orderBy('business_id')
            ->orderBy('id')
            ->get(['id', 'business_id'])
            ->each(function (object $product, int $index) {
                static $numbers = [];
                $numbers[$product->business_id] = ($numbers[$product->business_id] ?? 0) + 1;

                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['catalog_number' => $numbers[$product->business_id]]);
            });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['business_id', 'catalog_number']);
            $table->dropColumn('catalog_number');
        });
    }
};