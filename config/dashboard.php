<?php

return [
    'widgets' => [
        // Roles can be: admin, manager, user, etc. Fallback to 'default'
        'admin' => ['on_time_rate', 'high_risks', 'avg_utilization', 'top_variance'],
        'manager' => ['on_time_rate', 'avg_utilization', 'top_variance'],
        'user' => ['on_time_rate', 'avg_utilization'],
        'default' => ['on_time_rate', 'avg_utilization'],
    ],
];

