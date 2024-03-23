<?php

namespace App\Providers;

use App\Repositories\BreakTime\BreakTimeRepository;
use App\Repositories\BreakTime\BreakTimeRepositoryInterface;
use App\Repositories\Date\DateRepository;
use App\Repositories\Date\DateRepositoryInterface;
use App\Repositories\Remarks\RemarksRepository;
use App\Repositories\Remarks\RemarksRepositoryInterface;
use App\Repositories\Work\WorkRepository;
use App\Repositories\Work\WorkRepositoryInterface;
use App\Services\Pdf\PdfService;
use App\Services\Pdf\PdfServiceInterface;
use App\Usecases\BreakTime\BreakTimeUsecase;
use App\Usecases\BreakTime\BreakTimeUsecaseInterface;
use App\Usecases\Csv\CsvWorkUsecase;
use App\Usecases\Csv\CsvWorkUsecaseInterface;
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
        // 日付
        $this->app->bind(DateRepositoryInterface::class, DateRepository::class);

        // 勤怠
        $this->app->bind(WorkRepositoryInterface::class, WorkRepository::class);
        $this->app->bind(WorkUsecaseInterface::class, WorkUsecase::class);

        // 勤怠CSV
        $this->app->bind(CsvWorkUsecaseInterface::class, CsvWorkUsecase::class);

        // 休憩時間
        $this->app->bind(BreakTimeRepositoryInterface::class, BreakTimeRepository::class);
        $this->app->bind(BreakTimeUsecaseInterface::class, BreakTimeUsecase::class);

        // 備考
        $this->app->bind(RemarksRepositoryInterface::class, RemarksRepository::class);

        // PDF出力
        $this->app->bind(PdfServiceInterface::class, PdfService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
