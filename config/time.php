<?php

return [
    // Rounding increment in minutes for time entries when stopping a timer
    'rounding_minutes' => env('TIME_ROUNDING_MINUTES', 15),

    // Example rate card per role (IDR per hour)
    'rates_per_role' => [
        'Admin' => env('RATE_ADMIN', 250000),
        'Finance' => env('RATE_FINANCE', 200000),
        'Produksi' => env('RATE_PRODUKSI', 150000),
        'CS' => env('RATE_CS', 0),
    ],
];
