<?php

namespace App\Providers;

use App\Repositories\WorkRepository;
use App\Repositories\WorkRepositoryInterface;
use App\Usecases\WorkUsecase;
use App\Usecases\WorkUsecaseInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(WorkRepositoryInterface::class, WorkRepository::class);
        $this->app->bind(WorkUsecaseInterface::class, WorkUsecase::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
