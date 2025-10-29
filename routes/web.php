<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController as AuthRegisterController;
use App\Http\Controllers\ReportController;

// Guest routes (accessible without authentication)
Route::middleware('guest')->group(function () {
    // Login routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Register routes
    Route::get('/register', [AuthRegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthRegisterController::class, 'register']);
});

// Authenticated routes (require authentication + 2FA if enabled)
Route::middleware(['auth', App\Http\Middleware\SetTenant::class, App\Http\Middleware\EnsureTwoFactorVerified::class])->group(function () {
    // Logout route
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    // Dashboard route
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Executive Dashboard
    Route::get('/executive-dashboard', [App\Http\Controllers\DashboardController::class, 'executive'])
        ->name('dashboard.executive')
        ->middleware('permission:reports.view');

    // Projects (register create/edit before show to avoid 404)
    Route::resource('projects', App\Http\Controllers\ProjectController::class)
        ->except(['index','show'])->middleware('permission:projects.create|projects.edit|projects.delete');
    Route::resource('projects', App\Http\Controllers\ProjectController::class)
        ->only(['index','show'])->middleware('permission:projects.view');
    // Production Orders (alias UI for Projects)
    Route::get('production-orders', [App\Http\Controllers\ProjectController::class, 'index'])
        ->name('production-orders.index')->middleware('permission:projects.view');
    Route::get('production-orders/{project}', [App\Http\Controllers\ProjectController::class, 'show'])
        ->name('production-orders.show')->middleware('permission:projects.view');

    // Maklon: BPOM Registrations
    Route::resource('bpom', App\Http\Controllers\BpomRegistrationController::class)
        ->except(['show'])->middleware('permission:bpom.view|bpom.create|bpom.edit|bpom.delete');
    Route::get('bpom/{bpom}', [App\Http\Controllers\BpomRegistrationController::class, 'show'])
        ->name('bpom.show')->middleware('permission:bpom.view');
    Route::get('bpom-export', [App\Http\Controllers\BpomRegistrationController::class, 'export'])
        ->name('bpom.export')->middleware('permission:bpom.view');
    Route::get('bpom/{bpom}/download', [App\Http\Controllers\BpomRegistrationController::class, 'download'])
        ->name('bpom.download')->middleware('permission:bpom.view');
    Route::post('bpom/{bpom}/activate', [App\Http\Controllers\BpomRegistrationController::class, 'activate'])
        ->name('bpom.activate')->middleware('permission:bpom.edit');
    Route::post('bpom/{bpom}/revoke', [App\Http\Controllers\BpomRegistrationController::class, 'revoke'])
        ->name('bpom.revoke')->middleware('permission:bpom.edit');

    // Maklon: Production Batches
    Route::resource('batches', App\Http\Controllers\ProductionBatchController::class)
        ->middleware('permission:production.view|production.create|production.edit|production.delete');

    // Maklon: Suppliers
    Route::resource('suppliers', App\Http\Controllers\SupplierController::class)
        ->middleware('permission:supplier.view|supplier.create|supplier.edit|supplier.delete');
    Route::get('suppliers-export', [App\Http\Controllers\SupplierController::class, 'export'])
        ->name('suppliers.export')->middleware('permission:supplier.view');

    // Inventory (MVP)
    Route::resource('inventory', App\Http\Controllers\InventoryController::class)
        ->middleware('permission:inventory.view|inventory.create|inventory.edit|inventory.delete');
    Route::post('inventory/{inventory}/movement', [App\Http\Controllers\InventoryController::class, 'movement'])
        ->name('inventory.movement')->middleware('permission:inventory.edit');

    // BOM (v1 JSON)
    Route::resource('boms', App\Http\Controllers\BomController::class)
        ->middleware('permission:inventory.view|inventory.create|inventory.edit|inventory.delete');

    // Work Stations (capacity planning assets)
    Route::resource('work-stations', App\Http\Controllers\WorkStationController::class)
        ->except(['index','show'])->middleware('permission:production.create|production.edit|production.delete');
    Route::resource('work-stations', App\Http\Controllers\WorkStationController::class)
        ->only(['index','show'])->middleware('permission:production.view');

    // Quality Control
    Route::prefix('quality')->name('quality.')->group(function(){
        Route::get('checkpoints', [App\Http\Controllers\QualityController::class, 'checkpointsIndex'])->name('checkpoints.index');
        Route::get('checkpoints/create', [App\Http\Controllers\QualityController::class, 'checkpointsCreate'])->name('checkpoints.create');
        Route::post('checkpoints', [App\Http\Controllers\QualityController::class, 'checkpointsStore'])->name('checkpoints.store');
        Route::get('checkpoints/{checkpoint}', [App\Http\Controllers\QualityController::class, 'checkpointsShow'])->name('checkpoints.show');
        Route::get('checkpoints/{checkpoint}/edit', [App\Http\Controllers\QualityController::class, 'checkpointsEdit'])->name('checkpoints.edit');
        Route::put('checkpoints/{checkpoint}', [App\Http\Controllers\QualityController::class, 'checkpointsUpdate'])->name('checkpoints.update');
        Route::delete('checkpoints/{checkpoint}', [App\Http\Controllers\QualityController::class, 'checkpointsDestroy'])->name('checkpoints.destroy');

        Route::get('results', [App\Http\Controllers\QualityController::class, 'resultsIndex'])->name('results.index');
        Route::get('results/create', [App\Http\Controllers\QualityController::class, 'resultsCreate'])->name('results.create');
        Route::post('results', [App\Http\Controllers\QualityController::class, 'resultsStore'])->name('results.store');
        Route::get('results/{result}', [App\Http\Controllers\QualityController::class, 'resultsShow'])->name('results.show');
        Route::get('results/{result}/edit', [App\Http\Controllers\QualityController::class, 'resultsEdit'])->name('results.edit');
        Route::put('results/{result}', [App\Http\Controllers\QualityController::class, 'resultsUpdate'])->name('results.update');
        Route::delete('results/{result}', [App\Http\Controllers\QualityController::class, 'resultsDestroy'])->name('results.destroy');
    })->middleware('permission:qc.view|qc.create|qc.edit|qc.delete');

    // Deliveries
    Route::resource('deliveries', App\Http\Controllers\DeliveryController::class)
        ->middleware('permission:delivery.view|delivery.create|delivery.edit|delivery.delete');
    Route::post('deliveries/{delivery}/ship', [App\Http\Controllers\DeliveryController::class,'ship'])
        ->name('deliveries.ship')->middleware('permission:delivery.edit');
    Route::post('deliveries/{delivery}/delivered', [App\Http\Controllers\DeliveryController::class,'markDelivered'])
        ->name('deliveries.delivered')->middleware('permission:delivery.edit');
    Route::post('deliveries/{delivery}/returned', [App\Http\Controllers\DeliveryController::class,'markReturned'])
        ->name('deliveries.returned')->middleware('permission:delivery.edit');
    Route::post('deliveries/{delivery}/rejected', [App\Http\Controllers\DeliveryController::class,'markRejected'])
        ->name('deliveries.rejected')->middleware('permission:delivery.edit');

    // Tasks
    Route::resource('tasks', App\Http\Controllers\TaskController::class)
        ->except(['index','show'])->middleware('permission:tasks.create|tasks.edit|tasks.delete');
    Route::resource('tasks', App\Http\Controllers\TaskController::class)
        ->only(['index','show'])->middleware('permission:tasks.view');

    // Teams
    Route::resource('teams', App\Http\Controllers\TeamController::class)
        ->except(['index','show'])->middleware('permission:team.create|team.edit|team.delete');
    Route::resource('teams', App\Http\Controllers\TeamController::class)
        ->only(['index','show'])->middleware('permission:team.view');

    // Time entries
    Route::resource('time_entries', App\Http\Controllers\TimeEntryController::class)
        ->except(['index','show'])->middleware('permission:time.create|time.edit|time.delete');
    Route::resource('time_entries', App\Http\Controllers\TimeEntryController::class)
        ->only(['index','show'])->middleware('permission:time.view');

    // Attachments
    Route::resource('attachments', App\Http\Controllers\AttachmentController::class)
        ->except(['index','show'])->middleware('permission:attachments.create|attachments.delete');
    Route::resource('attachments', App\Http\Controllers\AttachmentController::class)
        ->only(['index','show'])->middleware('permission:attachments.view');

    // Notifications
    Route::resource('notifications', App\Http\Controllers\NotificationController::class)
        ->except(['index','show'])->middleware('permission:notifications.update');
    Route::resource('notifications', App\Http\Controllers\NotificationController::class)
        ->only(['index','show'])->middleware('permission:notifications.view');

    // Risks
    Route::resource('risks', App\Http\Controllers\RiskController::class)
        ->except(['index','show'])->middleware('permission:risks.create|risks.edit|risks.delete');
    Route::resource('risks', App\Http\Controllers\RiskController::class)
        ->only(['index','show'])->middleware('permission:risks.view');

    // Additional resource routes (must be before resources show route)
    Route::get('resources/utilization', [App\Http\Controllers\ResourceController::class, 'utilization'])
        ->name('resources.utilization')->middleware('permission:resources.view');
    Route::get('resources/reports', [App\Http\Controllers\ResourceController::class, 'reports'])
        ->name('resources.reports')->middleware('permission:resources.view');
    Route::get('resources/get-tasks', [App\Http\Controllers\ResourceController::class, 'getTasks'])
        ->name('resources.getTasks')->middleware('permission:resources.view');
    Route::get('resources/capacity', [App\Http\Controllers\ResourceController::class, 'capacity'])
        ->name('resources.capacity')->middleware('permission:resources.view');

    // Resources (allocations)
    Route::resource('resources', App\Http\Controllers\ResourceController::class)
        ->except(['index','show'])->middleware('permission:resources.create|resources.edit|resources.delete');
    Route::resource('resources', App\Http\Controllers\ResourceController::class)
        ->only(['index','show'])->middleware('permission:resources.view');

    // Additional resource routes (moved above)

    // Calendar routes
    Route::get('calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index')->middleware('permission:calendar.view');
    Route::get('calendar/events', [App\Http\Controllers\CalendarController::class, 'events'])->name('calendar.events.index')->middleware('permission:calendar.view');
    Route::get('calendar/events/create', [App\Http\Controllers\CalendarController::class, 'create'])->name('calendar.events.create')->middleware('permission:calendar.create');
    Route::post('calendar/events', [App\Http\Controllers\CalendarController::class, 'store'])->name('calendar.events.store')->middleware('permission:calendar.create');
    Route::get('calendar/events/{event}', [App\Http\Controllers\CalendarController::class, 'show'])->name('calendar.events.show')->middleware('permission:calendar.view');
    Route::get('calendar/events/{event}/edit', [App\Http\Controllers\CalendarController::class, 'edit'])->name('calendar.events.edit')->middleware('permission:calendar.edit');
    Route::put('calendar/events/{event}', [App\Http\Controllers\CalendarController::class, 'update'])->name('calendar.events.update')->middleware('permission:calendar.edit');
    Route::delete('calendar/events/{event}', [App\Http\Controllers\CalendarController::class, 'destroy'])->name('calendar.events.destroy')->middleware('permission:calendar.delete');
    Route::get('calendar/api/events', [App\Http\Controllers\CalendarController::class, 'getEvents'])->name('calendar.api.events')->middleware('permission:calendar.view');
    Route::post('calendar/api/events', [App\Http\Controllers\CalendarController::class, 'storeEvent'])->name('calendar.api.storeEvent')->middleware('permission:calendar.create');

    // Additional notification routes
    Route::patch('notifications/{notification}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markAsRead')->middleware('permission:notifications.update');
    Route::post('notifications/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead')->middleware('permission:notifications.update');
    Route::get('notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount')->middleware('permission:notifications.view');

    // Report routes
    Route::get('reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index')->middleware('permission:reports.view');
    Route::get('reports/project-progress', [App\Http\Controllers\ReportController::class, 'projectProgress'])->name('reports.projectProgress')->middleware('permission:reports.view');
    Route::get('reports/time-tracking', [App\Http\Controllers\ReportController::class, 'timeTracking'])->name('reports.timeTracking')->middleware('permission:reports.view');
    Route::get('reports/team-performance', [App\Http\Controllers\ReportController::class, 'teamPerformance'])->name('reports.teamPerformance')->middleware('permission:reports.view');
    Route::get('reports/deliveries', [App\Http\Controllers\ReportController::class, 'deliveryStatus'])->name('reports.deliveries')->middleware('permission:reports.view');
    Route::get('reports/project-progress/export', [App\Http\Controllers\ReportController::class, 'exportProjectProgress'])->name('reports.exportProjectProgress')->middleware('permission:reports.view');
    Route::get('reports/time-tracking/export', [App\Http\Controllers\ReportController::class, 'exportTimeTracking'])->name('reports.exportTimeTracking')->middleware('permission:reports.view');
    Route::get('reports/stakeholders', [App\Http\Controllers\ReportController::class, 'stakeholderEngagement'])->name('reports.stakeholders')->middleware('permission:reports.view');

    // Protected routes with role-based access
    Route::middleware('role:Admin')->group(function () {
        // Admin-only routes will be added here
    });

    Route::middleware('role:Manager')->group(function () {
        // Manager-only routes will be added here
    });

    Route::middleware('role:Developer')->group(function () {
        // Developer-only routes will be added here
    });
    // Capacity and Budget routes
    Route::get('resources/capacity', [App\Http\Controllers\ResourceController::class, 'capacity'])->name('resources.capacity')->middleware('permission:resources.view');

    Route::get('reports/financial', [App\Http\Controllers\ReportController::class, 'financial'])->name('reports.financial')->middleware('permission:reports.view');
    Route::get('reports/bpom-compliance', [App\Http\Controllers\ReportController::class, 'bpomCompliance'])->name('reports.bpom')->middleware('permission:reports.view');
    Route::get('reports/batch-qc', [App\Http\Controllers\ReportController::class, 'batchQcStatus'])->name('reports.batchQc')->middleware('permission:reports.view');
    Route::get('reports/inventory-health', [App\Http\Controllers\ReportController::class, 'inventoryHealth'])->name('reports.inventory')->middleware('permission:reports.view');

    // Budgets - ensure create/store registered before show
    Route::resource('budgets', App\Http\Controllers\BudgetController::class)
        ->only(['create','store'])->middleware('permission:budgets.create');
    Route::resource('budgets', App\Http\Controllers\BudgetController::class)
        ->only(['index','show'])->middleware('permission:budgets.view');
    Route::post('budgets/{budget}/categories', [App\Http\Controllers\BudgetController::class, 'storeCategory'])->name('budgets.categories.store')->middleware('permission:budgets.edit');
    Route::post('budgets/{budget}/expenses', [App\Http\Controllers\BudgetController::class, 'storeExpense'])->name('budgets.expenses.store')->middleware('permission:budgets.edit');
    Route::post('budgets/{budget}/approve', [App\Http\Controllers\BudgetController::class, 'approve'])->name('budgets.approve')->middleware('permission:budgets.edit');

    // Kanban
    Route::get('kanban', [App\Http\Controllers\KanbanController::class, 'index'])->name('kanban.index')->middleware('permission:tasks.view');
    Route::patch('kanban/tasks/{task}/status', [App\Http\Controllers\KanbanController::class, 'updateStatus'])->name('kanban.tasks.updateStatus')->middleware('permission:tasks.edit');

    // Time Tracking timer
    Route::post('tasks/{task}/time/start', [App\Http\Controllers\TimeEntryController::class, 'startTimer'])->name('tasks.time.start')->middleware('permission:time.create');
    Route::post('tasks/{task}/time/stop', [App\Http\Controllers\TimeEntryController::class, 'stopTimer'])->name('tasks.time.stop')->middleware('permission:time.edit');

    // Comments
    Route::post('comments', [App\Http\Controllers\CommentController::class, 'store'])->name('comments.store');
    Route::delete('comments/{comment}', [App\Http\Controllers\CommentController::class, 'destroy'])->name('comments.destroy');

    // Stakeholders
    Route::get('stakeholders/matrix', [App\Http\Controllers\StakeholderController::class, 'matrix'])->name('stakeholders.matrix')->middleware('permission:projects.view');
    Route::post('stakeholders/{stakeholder}/comms', [App\Http\Controllers\StakeholderController::class, 'addComm'])->name('stakeholders.comms.store')->middleware('permission:projects.edit');
    Route::resource('stakeholders', App\Http\Controllers\StakeholderController::class)->middleware('permission:projects.view');

    // Stakeholder Surveys
    Route::get('surveys', [App\Http\Controllers\StakeholderSurveyController::class, 'index'])->name('surveys.index')->middleware('permission:projects.view');
    Route::get('surveys/create', [App\Http\Controllers\StakeholderSurveyController::class, 'create'])->name('surveys.create')->middleware('permission:projects.edit');
    Route::post('surveys', [App\Http\Controllers\StakeholderSurveyController::class, 'store'])->name('surveys.store')->middleware('permission:projects.edit');
    Route::get('surveys/{survey}', [App\Http\Controllers\StakeholderSurveyController::class, 'show'])->name('surveys.show')->middleware('permission:projects.view');
    Route::post('surveys/{survey}/invite', [App\Http\Controllers\StakeholderSurveyController::class, 'invite'])->name('surveys.invite')->middleware('permission:projects.edit');
    Route::post('responses/{response}/submit', [App\Http\Controllers\StakeholderSurveyController::class, 'submit'])->name('surveys.responses.submit');

    // EVM
    Route::get('evm', [App\Http\Controllers\EvmController::class, 'index'])->name('evm.index')->middleware('permission:reports.view');
    Route::post('evm/baselines', [App\Http\Controllers\EvmController::class, 'storeBaseline'])->name('evm.baselines.store')->middleware('permission:reports.view');
    Route::post('evm/capture', [App\Http\Controllers\EvmController::class, 'capturePoint'])->name('evm.capture')->middleware('permission:reports.view');

    // Invoices
    Route::resource('invoices', App\Http\Controllers\InvoiceController::class)
        ->except(['index','show'])->middleware('permission:invoices.create|invoices.edit|invoices.delete');
    Route::resource('invoices', App\Http\Controllers\InvoiceController::class)
        ->only(['index','show'])->middleware('permission:invoices.view');
    Route::post('invoices/{invoice}/approve', [App\Http\Controllers\InvoiceController::class, 'approve'])->name('invoices.approve')->middleware('permission:invoices.approve');
    Route::post('invoices/{invoice}/paid', [App\Http\Controllers\InvoiceController::class, 'markPaid'])->name('invoices.paid')->middleware('permission:invoices.edit');
    Route::get('invoices/{invoice}/pdf', [App\Http\Controllers\InvoiceController::class, 'pdf'])->name('invoices.pdf')->middleware('permission:invoices.view');
    Route::post('invoices/{invoice}/items', [App\Http\Controllers\InvoiceController::class, 'addItem'])->name('invoices.items.add')->middleware('permission:invoices.edit');
    Route::delete('invoices/{invoice}/items/{item}', [App\Http\Controllers\InvoiceController::class, 'deleteItem'])->name('invoices.items.delete')->middleware('permission:invoices.edit');

    // Settings: API Tokens & Webhooks (admin/developer only)
    Route::middleware('role:Admin|Developer')->prefix('settings')->name('settings.')->group(function () {
        Route::get('api-tokens', [App\Http\Controllers\Settings\ApiTokenController::class, 'index'])->name('api-tokens.index');
        Route::post('api-tokens', [App\Http\Controllers\Settings\ApiTokenController::class, 'store'])->name('api-tokens.store');
        Route::delete('api-tokens/{api_token}', [App\Http\Controllers\Settings\ApiTokenController::class, 'destroy'])->name('api-tokens.destroy');

        Route::get('webhooks', [App\Http\Controllers\Settings\WebhookController::class, 'index'])->name('webhooks.index');
        Route::get('webhooks/create', [App\Http\Controllers\Settings\WebhookController::class, 'create'])->name('webhooks.create');
        Route::post('webhooks', [App\Http\Controllers\Settings\WebhookController::class, 'store'])->name('webhooks.store');
        Route::get('webhooks/{webhook}/edit', [App\Http\Controllers\Settings\WebhookController::class, 'edit'])->name('webhooks.edit');
        Route::put('webhooks/{webhook}', [App\Http\Controllers\Settings\WebhookController::class, 'update'])->name('webhooks.update');
        Route::delete('webhooks/{webhook}', [App\Http\Controllers\Settings\WebhookController::class, 'destroy'])->name('webhooks.destroy');
    });

    // Admin: User management (Admin only)
    Route::middleware('role:Admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', App\Http\Controllers\Admin\UserController::class)->except(['show']);
        Route::get('tenants', [App\Http\Controllers\Admin\TenantController::class, 'index'])->name('tenants.index');
        Route::post('tenants', [App\Http\Controllers\Admin\TenantController::class, 'store'])->name('tenants.store');
        Route::post('tenants/switch', [App\Http\Controllers\Admin\TenantController::class, 'switch'])->name('tenants.switch');
    });
});

// Public routes - redirect to login if not authenticated
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

// API routes (token-based)
Route::prefix('api')->middleware(['throttle:60,1', \App\Http\Middleware\ApiTokenAuth::class])->group(function () {
    Route::get('projects', [App\Http\Controllers\Api\ApiController::class, 'projects']);
    Route::get('projects/{project}', [App\Http\Controllers\Api\ApiController::class, 'projectShow']);
    Route::get('tasks', [App\Http\Controllers\Api\ApiController::class, 'tasks']);
    Route::get('tasks/{task}', [App\Http\Controllers\Api\ApiController::class, 'taskShow']);
    Route::get('time-entries', [App\Http\Controllers\Api\ApiController::class, 'timeEntries']);
    Route::get('budgets', [App\Http\Controllers\Api\ApiController::class, 'budgets']);
    // Calendar
    Route::get('calendar/events', [App\Http\Controllers\Api\ApiController::class, 'calendarList']);
    Route::post('calendar/events', [App\Http\Controllers\Api\ApiController::class, 'calendarCreate']);
    // Maklon additions
    Route::get('bpom', [App\Http\Controllers\Api\ApiController::class, 'bpomList']);
    Route::get('bpom/{bpom}', [App\Http\Controllers\Api\ApiController::class, 'bpomShow']);
    Route::get('suppliers', [App\Http\Controllers\Api\ApiController::class, 'suppliers']);
    Route::get('suppliers/{supplier}', [App\Http\Controllers\Api\ApiController::class, 'supplierShow']);
});

// 2FA routes
Route::middleware('auth')->group(function(){
    Route::get('settings/security/2fa', [App\Http\Controllers\Security\TwoFactorController::class, 'settings'])->name('2fa.settings');
    Route::post('settings/security/2fa/enable', [App\Http\Controllers\Security\TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post('settings/security/2fa/disable', [App\Http\Controllers\Security\TwoFactorController::class, 'disable'])->name('2fa.disable');
});
Route::get('2fa/verify', [App\Http\Controllers\Security\TwoFactorController::class, 'verifyForm'])->name('2fa.verify.form');
Route::post('2fa/verify', [App\Http\Controllers\Security\TwoFactorController::class, 'verify'])->name('2fa.verify');

// Public invoice view
Route::get('public/invoices/{token}', [App\Http\Controllers\InvoiceController::class, 'publicShow'])->name('invoices.public');

// Public project summary (signed URL only)
Route::get('public/projects/{project}/summary', [App\Http\Controllers\ReportController::class, 'clientSummary'])
    ->name('public.project.summary')
    ->middleware('signed');















