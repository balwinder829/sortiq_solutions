<?php

return [
    /*
     * Seconds for IMAP connect + stream reads (prevents hanging until PHP max_execution_time).
     */
    'imap_connection_timeout' => (int) env('GMAIL_IMAP_CONNECTION_TIMEOUT', 20),

    /*
     * Set false only if SSL verification fails on your machine (not recommended in production).
     */
    'imap_validate_cert' => filter_var(env('GMAIL_IMAP_VALIDATE_CERT', true), FILTER_VALIDATE_BOOL),

    'accounts' => [
        'sortiq' => [
            'label' => 'Queries',
            'email' => env('SORTIQ_GMAIL_ADDRESS', 'sortiqsolutions@gmail.com'),
            'imap_host' => env('SORTIQ_GMAIL_IMAP_HOST', 'imap.gmail.com'),
            'imap_port' => (int) env('SORTIQ_GMAIL_IMAP_PORT', 993),
            'imap_encryption' => env('SORTIQ_GMAIL_IMAP_ENCRYPTION', 'ssl'),
            'imap_username' => env('SORTIQ_GMAIL_IMAP_USERNAME', env('SORTIQ_GMAIL_ADDRESS', 'sortiqsolutions@gmail.com')),
            'imap_password' => env('SORTIQ_GMAIL_IMAP_PASSWORD'),
            'imap_timeout' => env('SORTIQ_GMAIL_IMAP_TIMEOUT'),
            'mailer' => 'gmail_sortiq',
        ],
        'hr' => [
            'label' => 'HR',
            'email' => env('HR_GMAIL_ADDRESS', 'hr.sortiqsolutions@gmail.com'),
            'imap_host' => env('HR_GMAIL_IMAP_HOST', 'imap.gmail.com'),
            'imap_port' => (int) env('HR_GMAIL_IMAP_PORT', 993),
            'imap_encryption' => env('HR_GMAIL_IMAP_ENCRYPTION', 'ssl'),
            'imap_username' => env('HR_GMAIL_IMAP_USERNAME', env('HR_GMAIL_ADDRESS', 'hr.sortiqsolutions@gmail.com')),
            'imap_password' => env('HR_GMAIL_IMAP_PASSWORD'),
            'imap_timeout' => env('HR_GMAIL_IMAP_TIMEOUT'),
            'mailer' => 'gmail_hr',
        ],
    ],
];