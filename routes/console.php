<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\AppointmentCompletionService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('appointments:complete-overdue', function (AppointmentCompletionService $completionService) {
    $completed = $completionService->completeOverdue();

    $this->info("Citas terminadas automaticamente: {$completed}");
})->purpose('Mark overdue appointments as completed and register their earnings');
