<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Task;
use App\Observers\TaskObserver;
use App\Models\QualityTask;
use App\Policies\QualityTaskPolicy;
use Illuminate\Support\Facades\Gate;


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
       Schema::defaultStringLength(125);
        Task::observe(TaskObserver::class);
        Gate::policy(QualityTask::class, QualityTaskPolicy::class);
    }
}
