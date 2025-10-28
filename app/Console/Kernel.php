<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    
        // BPOM expiry alerts daily at 08:00
        \->command('bpom:alert-expiry')->dailyAt('08:00');
        // Send weekly reports every Monday at 08:00
        $schedule->command('reports:send-weekly')->weeklyOn(1, '8:00');
        // Daily stakeholder comm reminders at 09:00
        $schedule->command('stakeholders:remind-comms')->dailyAt('9:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}

