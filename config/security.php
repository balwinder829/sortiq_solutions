<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist Enable/Disable
    |--------------------------------------------------------------------------
    |
    | When enabled, only requests from allowed IPs will be permitted.
    | Controlled via .env (IP_WHITELIST_ENABLED=true/false)
    |
    */

    'ip_whitelist_enabled' => env('IP_WHITELIST_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Allowed IPs (Static Fallback)
    |--------------------------------------------------------------------------
    |
    | These IPs are always allowed. You can also store IPs in DB (allowed_ips table).
    | Supports:
    | - Single IP: 203.0.113.10
    | - CIDR: 203.0.113.0/24
    | - IPv6
    |
    */

    'allowed_ips' => [
        '127.0.0.21',
        '::2',

        // Examples:
        // '192.168.1.1',
        // '192.168.1.0/24',
        // '203.0.113.10',
    ],

];