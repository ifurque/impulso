<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('public_background_image')->nullable()->after('public_background');
            $table->string('public_background_image_mode', 10)->nullable()->after('public_background_image');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['public_background_image', 'public_background_image_mode']);
        });
    }
};
