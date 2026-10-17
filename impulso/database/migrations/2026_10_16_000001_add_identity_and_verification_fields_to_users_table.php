<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 80)->nullable()->after('name');
            $table->string('last_name', 80)->nullable()->after('first_name');
            $table->string('document_number', 20)->nullable()->unique()->after('last_name');
            $table->string('address', 180)->nullable()->after('phone');
            $table->string('postal_code', 10)->nullable()->after('address');
            $table->string('city', 120)->nullable()->after('postal_code');
            $table->string('province', 80)->nullable()->after('city');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['document_number']);
            $table->dropColumn([
                'first_name',
                'last_name',
                'document_number',
                'address',
                'postal_code',
                'city',
                'province',
                'phone_verified_at',
            ]);
        });
    }
};
