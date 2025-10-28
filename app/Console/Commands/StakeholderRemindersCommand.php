<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StakeholderComm;
use Illuminate\Support\Facades\Log;

class StakeholderRemindersCommand extends Command
{
    protected $signature = 'stakeholders:remind-comms';
    protected $description = 'Log reminders for planned stakeholder communications (due or overdue)';

    public function handle(): int
    {
        $now = now();
        $due = StakeholderComm::whereNull('sent_at')
            ->whereNotNull('planned_at')
            ->where('planned_at','<=',$now)
            ->with('stakeholder')
            ->get();

        foreach ($due as $comm) {
            Log::info('Stakeholder comm reminder', [
                'stakeholder' => $comm->stakeholder->name ?? 'N/A',
                'project_id' => $comm->stakeholder->project_id ?? null,
                'subject' => $comm->subject,
                'planned_at' => (string) $comm->planned_at,
            ]);
        }

        $this->info('Reminders logged: '.$due->count());
        return self::SUCCESS;
    }
}

