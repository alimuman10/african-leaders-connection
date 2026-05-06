<?php

namespace Database\Seeders;

use App\Models\AdvocacySection;
use App\Models\ActivityLog;
use App\Models\LeadershipResource;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Story;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view dashboard',
            'edit own profile',
            'create submission',
            'comment',
            'react',
            'report content',
            'register for events',
            'save opportunities',
            'view moderator tools',
            'review reports',
            'hide content',
            'warn members',
            'submit moderation report',
            'approve/reject comments',
            'full platform access',
            'manage users',
            'manage moderators',
            'manage content',
            'manage community',
            'manage contact messages',
            'manage media',
            'manage events',
            'manage opportunities',
            'manage campaigns',
            'manage settings',
            'view analytics',
            'manage homepage',
            'view audit logs',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $memberPermissions = [
            'view dashboard',
            'edit own profile',
            'create submission',
            'comment',
            'react',
            'report content',
            'register for events',
            'save opportunities',
        ];

        $moderatorPermissions = [
            ...$memberPermissions,
            'view moderator tools',
            'review reports',
            'hide content',
            'warn members',
            'submit moderation report',
            'approve/reject comments',
        ];

        $roles = [
            'Member' => $memberPermissions,
            'Moderator' => $moderatorPermissions,
            'Super Admin' => $permissions,
        ];

        foreach ($roles as $name => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }

        $admin = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@africanleadersconnection.org')],
            [
                'name' => env('ADMIN_NAME', 'African Leaders Connection Admin'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'ChangeMe-Strong-2026!')),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['Super Admin']);

        $services = [
            'Leadership Development',
            'Executive Coaching',
            'Youth Leadership Training',
            'Governance Advisory',
            'Policy Awareness',
            'Community Development',
            'Digital Transformation',
            'Website Development',
            'Web Application Development',
            'Digital Marketing',
            'SEO Services',
            'Social Media Strategy',
            'Personal Branding for Leaders',
            'AI and Innovation Consulting',
            'Research and Thought Leadership',
            'Institutional Capacity Building',
            'Training and Workshops',
            'Public Speaking and Communication',
            'Community Impact Programs',
        ];

        foreach ($services as $index => $title) {
            Service::firstOrCreate(
                ['slug' => str($title)->slug()],
                [
                    'title' => $title,
                    'category' => 'Core Services',
                    'summary' => "{$title} for leaders, institutions, and communities building practical African progress.",
                    'sort_order' => $index + 1,
                    'active' => true,
                ]
            );
        }

        foreach (['Leadership Accountability', 'Youth Civic Leadership', 'Community Development', 'Policy Awareness', 'Institutional Responsibility', 'Unity and Peacebuilding'] as $index => $title) {
            AdvocacySection::firstOrCreate(
                ['slug' => str($title)->slug()],
                [
                    'title' => $title,
                    'summary' => 'Advocacy rooted in service, responsibility, and measurable progress.',
                    'sort_order' => $index + 1,
                    'active' => true,
                ]
            );
        }

        Setting::firstOrCreate(['key' => 'platform.tagline'], ['value' => 'Leadership. Unity. Progress.', 'group' => 'brand']);
        Setting::firstOrCreate(['key' => 'platform.region_focus'], ['value' => ['Pan-African', 'Sierra Leone', 'West Africa'], 'group' => 'brand']);

        $stories = [
            [
                'title' => 'Community Leadership Through Practical Service',
                'excerpt' => 'A story of local leadership rooted in listening, trust, and measurable community action.',
                'country' => 'Sierra Leone',
                'region' => 'West Africa',
                'featured' => true,
            ],
            [
                'title' => 'Youth Voices Shaping Civic Responsibility',
                'excerpt' => 'Young leaders building confidence, discipline, and participation across community spaces.',
                'country' => 'Ghana',
                'region' => 'West Africa',
                'featured' => false,
            ],
            [
                'title' => 'Innovation, Education, and African Progress',
                'excerpt' => 'How digital tools and mentorship can expand opportunity for institutions and learners.',
                'country' => 'Kenya',
                'region' => 'East Africa',
                'featured' => false,
            ],
        ];

        foreach ($stories as $story) {
            Story::firstOrCreate(
                ['slug' => str($story['title'])->slug()],
                [
                    ...$story,
                    'author_id' => $admin->id,
                    'body' => 'African leadership becomes visible when people act with courage, responsibility, and service. This seeded story provides a starting point for editorial content, impact storytelling, and credibility-building narratives.',
                    'status' => 'published',
                    'published_at' => now(),
                ]
            );
        }

        $projects = [
            ['title' => 'Youth Leadership Mentorship Network', 'status' => 'active', 'country' => 'Sierra Leone', 'region' => 'West Africa'],
            ['title' => 'Digital Visibility for Community Organizations', 'status' => 'planned', 'country' => 'Pan-African', 'region' => 'Africa'],
            ['title' => 'Leadership and Civic Dialogue Series', 'status' => 'active', 'country' => 'Liberia', 'region' => 'West Africa'],
        ];

        foreach ($projects as $project) {
            Project::firstOrCreate(
                ['slug' => str($project['title'])->slug()],
                [
                    ...$project,
                    'summary' => "{$project['title']} supports leadership, unity, and practical progress.",
                    'description' => 'A structured initiative designed to connect leaders, strengthen communities, and turn ideas into measurable impact.',
                    'impact_metrics' => ['target_participants' => 250, 'communities' => 5],
                    'location' => $project['country'],
                ]
            );
        }

        foreach ([
            ['name' => 'Aminata Kamara', 'role' => 'Community Program Lead', 'organization' => 'Youth Civic Forum', 'quote' => 'African Leaders Connection gives leadership a language of service, trust, and practical action.'],
            ['name' => 'Daniel Mensah', 'role' => 'Innovation Advisor', 'organization' => 'Digital Africa Network', 'quote' => 'The platform connects leadership with technology in a way that feels credible and useful.'],
        ] as $testimonial) {
            Testimonial::firstOrCreate(['name' => $testimonial['name']], [...$testimonial, 'featured' => true, 'active' => true]);
        }

        foreach ([
            ['name' => 'Pan-African Leadership Forum', 'country' => 'Pan-African', 'sector' => 'Leadership Development'],
            ['name' => 'Community Innovation Hub', 'country' => 'Sierra Leone', 'sector' => 'Technology and Training'],
            ['name' => 'Youth Mentorship Network', 'country' => 'Ghana', 'sector' => 'Youth Leadership'],
        ] as $partner) {
            Partner::firstOrCreate(['name' => $partner['name']], [...$partner, 'active' => true]);
        }

        foreach ([
            ['title' => 'Responsible Leadership Field Guide', 'type' => 'guide'],
            ['title' => 'Community Impact Planning Template', 'type' => 'template'],
            ['title' => 'Public Speaking for Emerging Leaders', 'type' => 'training'],
        ] as $resource) {
            LeadershipResource::firstOrCreate(
                ['title' => $resource['title']],
                [
                    ...$resource,
                    'summary' => 'A practical resource for members of the African Leaders Connection community.',
                    'member_only' => true,
                    'active' => true,
                ]
            );
        }

        ActivityLog::firstOrCreate(
            ['action' => 'platform.seeded'],
            [
                'user_id' => $admin->id,
                'properties' => ['message' => 'Initial platform roles, services, stories, projects, resources, and partners seeded.'],
                'ip_address' => '127.0.0.1',
            ]
        );
    }
}
