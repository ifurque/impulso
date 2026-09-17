<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('public_pattern_image')->nullable()->after('public_background_pattern_size');
            $table->unsignedSmallInteger('public_pattern_size')->nullable()->after('public_pattern_image');
            $table->unsignedSmallInteger('public_pattern_gap_x')->nullable()->after('public_pattern_size');
            $table->unsignedSmallInteger('public_pattern_gap_y')->nullable()->after('public_pattern_gap_x');
            $table->unsignedSmallInteger('public_pattern_scale')->nullable()->after('public_pattern_gap_y');
            $table->smallInteger('public_pattern_rotation')->nullable()->after('public_pattern_scale');
            $table->unsignedSmallInteger('public_pattern_opacity')->nullable()->after('public_pattern_rotation');
            $table->smallInteger('public_pattern_offset_x')->nullable()->after('public_pattern_opacity');
            $table->smallInteger('public_pattern_offset_y')->nullable()->after('public_pattern_offset_x');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'public_pattern_image', 'public_pattern_size', 'public_pattern_gap_x',
                'public_pattern_gap_y', 'public_pattern_scale', 'public_pattern_rotation',
                'public_pattern_opacity', 'public_pattern_offset_x', 'public_pattern_offset_y',
            ]);
        });
    }
};
