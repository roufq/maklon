<?php

return [
    // Core maklon modules remain enabled implicitly
    'finance' => true, // budgets + invoices
    'calendar' => true,

    // Disable non-maklon modules
    'stakeholders' => false,
    'surveys' => false,
    'evm' => false,
    'comments' => false,
    'risks' => false,
    'teams' => false,
    'notifications' => false,
    'resources' => false,
    'attachments' => false,
    'api_tokens' => false,
    'public_sharing' => false,

    // Reports toggles
    'reports' => [
        'team_performance' => false,
        'time_tracking' => false,
        'stakeholder_engagement' => false,
    ],
];

