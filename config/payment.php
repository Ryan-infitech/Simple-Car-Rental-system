<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the car rental payment system.
    | You can configure bank accounts, payment methods, and other payment
    | related settings here.
    |
    */

    'default_currency' => 'IDR',
    'currency_symbol' => 'Rp',

    /*
    |--------------------------------------------------------------------------
    | Bank Accounts
    |--------------------------------------------------------------------------
    |
    | Configure your company's bank accounts for different payment methods.
    | These will be displayed to customers during checkout.
    |
    */
    'bank_accounts' => [
        'bca' => [
            'name' => 'Bank BCA',
            'account_number' => env('BCA_ACCOUNT_NUMBER', '1234567890'),
            'account_name' => env('BCA_ACCOUNT_NAME', 'CV Rental Mobil Sejahtera'),
            'code' => 'BCA',
            'logo' => 'images/banks/bca.png',
            'color' => '#003d82',
        ],
        'mandiri' => [
            'name' => 'Bank Mandiri',
            'account_number' => env('MANDIRI_ACCOUNT_NUMBER', '0987654321'),
            'account_name' => env('MANDIRI_ACCOUNT_NAME', 'CV Rental Mobil Sejahtera'),
            'code' => 'MANDIRI',
            'logo' => 'images/banks/mandiri.png',
            'color' => '#fdb913',
        ],
        'bni' => [
            'name' => 'Bank BNI',
            'account_number' => env('BNI_ACCOUNT_NUMBER', '1122334455'),
            'account_name' => env('BNI_ACCOUNT_NAME', 'CV Rental Mobil Sejahtera'),
            'code' => 'BNI',
            'logo' => 'images/banks/bni.png',
            'color' => '#e8570f',
        ],
        'bri' => [
            'name' => 'Bank BRI',
            'account_number' => env('BRI_ACCOUNT_NUMBER', '5566778899'),
            'account_name' => env('BRI_ACCOUNT_NAME', 'CV Rental Mobil Sejahtera'),
            'code' => 'BRI',
            'logo' => 'images/banks/bri.png',
            'color' => '#003d6b',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Settings
    |--------------------------------------------------------------------------
    |
    | Configure payment-related settings such as timeouts, fees, etc.
    |
    */
    'payment_timeout_hours' => env('PAYMENT_TIMEOUT_HOURS', 24),
    'auto_cancel_unpaid_bookings' => env('AUTO_CANCEL_UNPAID_BOOKINGS', true),
    'require_payment_proof' => true,
    'max_payment_proof_size' => 2048, // KB

    /*
    |--------------------------------------------------------------------------
    | Supported Payment Methods
    |--------------------------------------------------------------------------
    |
    | List of supported payment methods for the rental system.
    |
    */
    'supported_methods' => [
        'bca' => 'Bank BCA',
        'mandiri' => 'Bank Mandiri',
        'bni' => 'Bank BNI',
        'bri' => 'Bank BRI',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Status
    |--------------------------------------------------------------------------
    |
    | Available payment statuses and their display names.
    |
    */
    'statuses' => [
        'pending' => 'Menunggu Pembayaran',
        'verified' => 'Pembayaran Terverifikasi',
        'rejected' => 'Pembayaran Ditolak',
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    |
    | Settings for invoice generation and formatting.
    |
    */
    'invoice' => [
        'prefix' => 'INV',
        'company_name' => env('COMPANY_NAME', 'CV Rental Mobil Sejahtera'),
        'company_address' => env('COMPANY_ADDRESS', 'Jl. Contoh No. 123, Jakarta'),
        'company_phone' => env('COMPANY_PHONE', '021-12345678'),
        'company_email' => env('COMPANY_EMAIL', 'info@rentalmobil.com'),
        'tax_rate' => 0, // 0% for no tax, 0.1 for 10%, etc.
    ],
];
