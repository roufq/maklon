<?php

return [
    // Work-In-Progress limits per status column
    'wip_limits' => [
        'pending' => env('KANBAN_WIP_PENDING', 999),
        'in_progress' => env('KANBAN_WIP_IN_PROGRESS', 3),
        'completed' => env('KANBAN_WIP_COMPLETED', 999),
        'cancelled' => env('KANBAN_WIP_CANCELLED', 999),
    ],
];

