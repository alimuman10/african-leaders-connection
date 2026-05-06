<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'failed_login_attempts')) {
                $table->unsignedSmallInteger('failed_login_attempts')->default(0)->after('last_login_at');
            }

            if (! Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            }

            if (! Schema::hasColumn('users', 'two_factor_secret')) {
                $table->text('two_factor_secret')->nullable()->after('locked_until');
            }

            if (! Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            }

            if (! Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            }

            if (! Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        if (! Schema::hasTable('user_profiles')) {
            Schema::create('user_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
                $table->string('phone')->nullable();
                $table->string('country')->nullable();
                $table->string('city')->nullable();
                $table->string('profession')->nullable();
                $table->string('organization')->nullable();
                $table->string('leadership_interest')->nullable();
                $table->string('leadership_category')->nullable();
                $table->string('professional_title')->nullable();
                $table->string('profile_photo_path')->nullable();
                $table->text('bio')->nullable();
                $table->json('skills')->nullable();
                $table->json('interests')->nullable();
                $table->json('social_links')->nullable();
                $table->string('portfolio_link')->nullable();
                $table->json('causes_supported')->nullable();
                $table->unsignedTinyInteger('completion_percentage')->default(0);
                $table->timestamps();
            });

            if (Schema::hasTable('profiles')) {
                DB::table('profiles')->orderBy('id')->get()->each(function ($profile) {
                    DB::table('user_profiles')->updateOrInsert(
                        ['user_id' => $profile->user_id],
                        collect((array) $profile)
                            ->except(['id'])
                            ->merge(['created_at' => $profile->created_at, 'updated_at' => $profile->updated_at])
                            ->all()
                    );
                });
            }
        }

        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->index();
            $table->string('status')->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('logged_in_at')->nullable();
            $table->timestamps();
        });

        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event')->index();
            $table->string('severity')->default('info')->index();
            $table->nullableMorphs('subject');
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_events');
        Schema::dropIfExists('login_histories');
        Schema::dropIfExists('user_profiles');

        Schema::table('users', function (Blueprint $table) {
            $columns = ['failed_login_attempts', 'locked_until', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at', 'deleted_at'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
