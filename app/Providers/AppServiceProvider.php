<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Project;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Models\ProjectBudget;
use App\Jobs\SendWebhookEvent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Outbound webhook dispatchers
        Project::created(function ($model) {
            SendWebhookEvent::dispatch('project.created', $model->toArray());
        });
        Project::updated(function ($model) {
            SendWebhookEvent::dispatch('project.updated', $model->toArray());
        });

        Task::created(function ($model) {
            SendWebhookEvent::dispatch('task.created', $model->toArray());
        });
        Task::updated(function ($model) {
            SendWebhookEvent::dispatch('task.updated', $model->toArray());
        });

        TimeEntry::created(function ($model) {
            SendWebhookEvent::dispatch('time.created', $model->toArray());
        });
        TimeEntry::updated(function ($model) {
            SendWebhookEvent::dispatch('time.updated', $model->toArray());
        });

        ProjectBudget::created(function ($model) {
            SendWebhookEvent::dispatch('budget.created', $model->toArray());
        });
        ProjectBudget::updated(function ($model) {
            SendWebhookEvent::dispatch('budget.updated', $model->toArray());
        });
    }
}
