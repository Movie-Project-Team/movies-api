<?php

namespace App\Providers;

use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Queue::after(function (JobProcessed $event) {
            // Log::info("Job {$event->job->getJobId()} completed successfully.");
            echo "Job {$event->job->getJobId()} completed successfully.";
        });

        Queue::failing(function (JobFailed $event) {
            echo "Job {$event->job->getJobId()} failed with error: " . $event->exception->getMessage();
            // Log::error("Job {$event->job->getJobId()} failed with error: " . $event->exception->getMessage());
        });
    }
}
