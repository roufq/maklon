<?php

return [
    // Rounding increment in minutes for time entries when stopping a timer
    'rounding_minutes' => env('TIME_ROUNDING_MINUTES', 15),

    // Example rate card per role (IDR per hour)
    'rates_per_role' => [
        'Admin' => env('RATE_ADMIN', 250000),
        'Manager' => env('RATE_MANAGER', 200000),
        'Developer' => env('RATE_DEVELOPER', 150000),
        'Client' => env('RATE_CLIENT', 0),
    ],
];

