<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('public_primary_color', 7)->nullable()->after('public_font_family');
            $table->string('public_secondary_color', 7)->nullable()->after('public_primary_color');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['public_primary_color', 'public_secondary_color']);
        });
    }
};
