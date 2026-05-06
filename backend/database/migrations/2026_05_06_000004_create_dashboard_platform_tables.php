<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            foreach ([
                'city' => fn () => $table->string('city')->nullable()->after('country'),
                'leadership_category' => fn () => $table->string('leadership_category')->nullable()->after('leadership_interest'),
                'professional_title' => fn () => $table->string('professional_title')->nullable()->after('leadership_category'),
                'skills' => fn () => $table->json('skills')->nullable()->after('bio'),
                'interests' => fn () => $table->json('interests')->nullable()->after('skills'),
                'social_links' => fn () => $table->json('social_links')->nullable()->after('interests'),
                'portfolio_link' => fn () => $table->string('portfolio_link')->nullable()->after('social_links'),
                'causes_supported' => fn () => $table->json('causes_supported')->nullable()->after('portfolio_link'),
                'completion_percentage' => fn () => $table->unsignedTinyInteger('completion_percentage')->default(0)->after('causes_supported'),
            ] as $column => $definition) {
                if (! Schema::hasColumn('profiles', $column)) {
                    $definition();
                }
            }
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('category')->nullable()->index();
            $table->string('status')->default('draft')->index();
            $table->boolean('featured')->default(false)->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->text('body');
            $table->string('status')->default('pending')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('reactable');
            $table->string('type')->default('like');
            $table->timestamps();
            $table->unique(['user_id', 'reactable_type', 'reactable_id', 'type'], 'unique_user_reaction');
        });

        Schema::create('saved_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'post_id']);
        });

        Schema::create('leadership_stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('country')->nullable()->index();
            $table->text('summary')->nullable();
            $table->longText('body');
            $table->string('status')->default('pending')->index();
            $table->text('review_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('community_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('country')->nullable()->index();
            $table->string('category')->nullable()->index();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->string('location')->nullable();
            $table->boolean('is_online')->default(false);
            $table->json('speakers')->nullable();
            $table->json('resources')->nullable();
            $table->string('status')->default('scheduled')->index();
            $table->timestamp('starts_at')->nullable()->index();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('registered')->index();
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();
            $table->unique(['event_id', 'user_id']);
        });

        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->index();
            $table->string('country')->nullable()->index();
            $table->string('eligibility')->nullable();
            $table->text('summary')->nullable();
            $table->string('external_url')->nullable();
            $table->timestamp('deadline_at')->nullable()->index();
            $table->string('status')->default('active')->index();
            $table->boolean('featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('advocacy_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('country')->nullable()->index();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->json('impact_metrics')->nullable();
            $table->string('status')->default('active')->index();
            $table->boolean('featured')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('campaign_supporters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('advocacy_campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['advocacy_campaign_id', 'user_id'], 'unique_campaign_supporter');
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->longText('body');
            $table->string('audience')->default('all')->index();
            $table->string('country')->nullable()->index();
            $table->string('category')->nullable()->index();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->nullableMorphs('reportable');
            $table->string('reason')->index();
            $table->text('details')->nullable();
            $table->string('status')->default('open')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('moderator_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email');
            $table->string('token')->unique();
            $table->string('status')->default('invited')->index();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('moderation_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moderator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('report_id')->nullable()->constrained()->nullOnDelete();
            $table->nullableMorphs('actionable');
            $table->string('action')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title')->nullable();
            $table->text('subtitle')->nullable();
            $table->json('content')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->string('group')->default('general')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'platform_settings',
            'homepage_sections',
            'moderation_actions',
            'moderator_invitations',
            'reports',
            'notifications',
            'announcements',
            'campaign_supporters',
            'advocacy_campaigns',
            'opportunities',
            'event_registrations',
            'events',
            'community_projects',
            'leadership_stories',
            'saved_posts',
            'reactions',
            'comments',
            'posts',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
