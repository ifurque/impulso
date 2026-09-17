<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('profile_photo_style', 20)->nullable()->after('profile_photo');
            $table->unsignedTinyInteger('profile_photo_position_x')->nullable()->after('profile_photo_style');
            $table->unsignedTinyInteger('profile_photo_position_y')->nullable()->after('profile_photo_position_x');
            $table->unsignedTinyInteger('profile_photo_zoom')->nullable()->after('profile_photo_position_y');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'profile_photo_style',
                'profile_photo_position_x',
                'profile_photo_position_y',
                'profile_photo_zoom',
            ]);
        });
    }
};
