<?php

namespace App\Providers;

use App\Models\DocumentApproval;
use App\Models\QualityTask;
use App\Models\Task;
use App\Observers\TaskObserver;
use App\Policies\QualityTaskPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


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

        // Compartir el conteo de firmas pendientes a todas las vistas autenticadas
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $count = DocumentApproval::where('user_id', $user->id)
                    ->where('status', DocumentApproval::STATUS_PENDING)
                    ->whereHas('version', fn ($q) =>
                        $q->whereIn('status', ['in_review', 'in_approval'])
                    )
                    ->count();

                $view->with('pendingSignaturesCount', $count);
            }
        });
    }
}
