<?php

return [
    'super_admin_email' => env('SUPER_ADMIN_EMAIL', 'mansarayalimu903@gmail.com'),
    'super_admin_name' => env('SUPER_ADMIN_NAME', 'African Leaders Connection Super Admin'),
    'super_admin_password' => env('SUPER_ADMIN_PASSWORD'),
    'max_failed_login_attempts' => (int) env('AUTH_MAX_FAILED_ATTEMPTS', 5),
    'lockout_minutes' => (int) env('AUTH_LOCKOUT_MINUTES', 15),
    'require_verified_email' => (bool) env('AUTH_REQUIRE_VERIFIED_EMAIL', false),
];
