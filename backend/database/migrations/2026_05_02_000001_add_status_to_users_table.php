<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('country')->nullable()->after('phone');
            $table->string('profession')->nullable()->after('country');
            $table->string('organization')->nullable()->after('profession');
            $table->string('leadership_interest')->nullable()->after('organization');
            $table->string('status')->default('pending')->after('password');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'country',
                'profession',
                'organization',
                'leadership_interest',
                'status',
                'last_login_at',
            ]);
        });
    }
};
