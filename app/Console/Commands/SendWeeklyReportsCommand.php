<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendWeeklyReports;

class SendWeeklyReportsCommand extends Command
{
    protected $signature = 'reports:send-weekly';
    protected $description = 'Queue weekly project progress and time tracking summary emails';

    public function handle(): int
    {
        dispatch(new SendWeeklyReports());
        $this->info('Weekly reports job dispatched.');
        return self::SUCCESS;
    }
}

