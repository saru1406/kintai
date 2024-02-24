<?php

namespace App\Providers;

use App\Repositories\Remarks\RemarksRepository;
use App\Repositories\Remarks\RemarksRepositoryInterface;
use App\Repositories\Work\WorkRepository;
use App\Repositories\Work\WorkRepositoryInterface;
use App\Usecases\Work\WorkUsecase;
use App\Usecases\Work\WorkUsecaseInterface;
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

        $this->app->bind(RemarksRepositoryInterface::class, RemarksRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
