<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('public_navbar_color', 7)->nullable()->after('public_background');
            $table->string('public_posts_background', 7)->nullable()->after('public_navbar_color');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['public_navbar_color', 'public_posts_background']);
        });
    }
};
