<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiToken;
use App\Models\User;

class CreateApiTokenCommand extends Command
{
    protected $signature = 'api:token {user_id} {name}';
    protected $description = 'Create an API token for a user (prints plaintext token once)';

    public function handle(): int
    {
        $userId = (int) $this->argument('user_id');
        $name = (string) $this->argument('name');

        $user = User::find($userId);
        if (!$user) {
            $this->error('User not found');
            return self::FAILURE;
        }

        $plain = bin2hex(random_bytes(32));
        $hash = hash('sha256', $plain);
        ApiToken::create(['user_id' => $user->id, 'name' => $name, 'token_hash' => $hash]);
        $this->info('Token created for user '.$user->id.' ('.$user->email.')');
        $this->line('Plain token (save now, not shown again):');
        $this->line($plain);
        return self::SUCCESS;
    }
}

