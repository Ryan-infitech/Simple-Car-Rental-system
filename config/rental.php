<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Car Rental Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options specific to the car rental
    | system functionality.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Booking Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for booking-related functionality.
    |
    */
    'booking' => [
        'min_rental_days' => env('MIN_RENTAL_DAYS', 1),
        'max_rental_days' => env('MAX_RENTAL_DAYS', 30),
        'advance_booking_days' => env('ADVANCE_BOOKING_DAYS', 90),
        'auto_confirm_bookings' => env('AUTO_CONFIRM_BOOKINGS', false),
        'require_admin_approval' => env('REQUIRE_ADMIN_APPROVAL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Car Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for car-related functionality.
    |
    */
    'cars' => [
        'max_images_per_car' => env('MAX_IMAGES_PER_CAR', 5),
        'image_max_size_kb' => env('CAR_IMAGE_MAX_SIZE_KB', 2048),
        'image_dimensions' => [
            'width' => env('CAR_IMAGE_WIDTH', 800),
            'height' => env('CAR_IMAGE_HEIGHT', 600),
        ],
        'thumbnail_dimensions' => [
            'width' => env('CAR_THUMBNAIL_WIDTH', 300),
            'height' => env('CAR_THUMBNAIL_HEIGHT', 200),
        ],
        'default_image' => 'images/default-car.jpg',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for the notification system.
    |
    */
    'notifications' => [
        'auto_delete_read_after_days' => env('NOTIFICATION_AUTO_DELETE_DAYS', 30),
        'max_notifications_per_user' => env('MAX_NOTIFICATIONS_PER_USER', 100),
        'types' => [
            'booking' => 'Booking',
            'payment' => 'Pembayaran',
            'general' => 'Umum',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for user-related functionality.
    |
    */
    'users' => [
        'require_email_verification' => env('REQUIRE_EMAIL_VERIFICATION', false),
        'require_identity_verification' => env('REQUIRE_IDENTITY_VERIFICATION', true),
        'identity_number_length' => 16, // KTP Indonesia
        'phone_number_formats' => [
            '/^(\+62|62|0)[0-9]{8,12}$/' => 'Format Indonesia',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Rules
    |--------------------------------------------------------------------------
    |
    | Business logic configuration for the rental system.
    |
    */
    'business_rules' => [
        'operating_hours' => [
            'start' => env('BUSINESS_HOURS_START', '08:00'),
            'end' => env('BUSINESS_HOURS_END', '17:00'),
        ],
        'operating_days' => [
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => true,
            'sunday' => false,
        ],
        'holiday_surcharge' => env('HOLIDAY_SURCHARGE_PERCENT', 0),
        'weekend_surcharge' => env('WEEKEND_SURCHARGE_PERCENT', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Storage
    |--------------------------------------------------------------------------
    |
    | Configuration for file storage paths and settings.
    |
    */
    'storage' => [
        'car_images_path' => 'cars',
        'payment_proofs_path' => 'payment_proofs',
        'user_documents_path' => 'documents',
        'temp_path' => 'temp',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security-related configuration options.
    |
    */
    'security' => [
        'max_login_attempts' => env('MAX_LOGIN_ATTEMPTS', 5),
        'lockout_duration' => env('LOCKOUT_DURATION_MINUTES', 15),
        'password_reset_timeout' => env('PASSWORD_RESET_TIMEOUT_MINUTES', 60),
        'session_timeout' => env('SESSION_TIMEOUT_MINUTES', 120),
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    |
    | Default pagination settings for different sections.
    |
    */
    'pagination' => [
        'cars_per_page' => env('CARS_PER_PAGE', 12),
        'bookings_per_page' => env('BOOKINGS_PER_PAGE', 15),
        'customers_per_page' => env('CUSTOMERS_PER_PAGE', 15),
        'payments_per_page' => env('PAYMENTS_PER_PAGE', 15),
        'notifications_per_page' => env('NOTIFICATIONS_PER_PAGE', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Cache configuration for performance optimization.
    |
    */
    'cache' => [
        'cars_cache_ttl' => env('CARS_CACHE_TTL', 3600), // 1 hour
        'stats_cache_ttl' => env('STATS_CACHE_TTL', 1800), // 30 minutes
        'notifications_cache_ttl' => env('NOTIFICATIONS_CACHE_TTL', 300), // 5 minutes
    ],
];
