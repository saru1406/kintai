<?php

declare(strict_types=1);

namespace App\Usecases\Work;

use App\Models\Date;
use App\Models\Work;
use App\Repositories\BreakTime\BreakTimeRepositoryInterface;
use App\Repositories\Date\DateRepositoryInterface;
use App\Repositories\Remarks\RemarksRepositoryInterface;
use App\Repositories\Work\WorkRepositoryInterface;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Uid\Ulid;

class WorkUsecase implements WorkUsecaseInterface
{
    public function __construct(
        private WorkRepositoryInterface $workRepository,
        private RemarksRepositoryInterface $remarksRepository,
        private BreakTimeRepositoryInterface $breakTimeRepository,
        private DateRepositoryInterface $dateRepository,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function storeStart(string $startDate, ?string $remarks): void
    {
        $startDateTime = new DateTime($startDate);
        $userId = Auth::id();

        $existsDate = $this->dateRepository->existsByDate($startDateTime->format('Y-m-d'));
        $this->checkDate($existsDate);
        $currentDate = $this->dateRepository->firstOrFailByCurrentDate(Carbon::now()->format('Y-m-d'));

        $existsWork = $this->workRepository->exists($userId);
        if ($existsWork) {
            $work = $this->workRepository->firstOrFail($userId);
            $this->checkNotEndDate($work, $userId);
        }
        $params = [
            'user_id' => $userId,
            'date_id' => $currentDate->id,
            'start' => $startDateTime->format('Y-m-d H:i'),
        ];

        try {
            DB::transaction(function () use ($params, $remarks) {
                $work = $this->workRepository->store($params);
                $this->storeRemarks($work, $remarks);
            });
        } catch (\Throwable $e) {
            Log::info($e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function storeEnd(string $endDate, ?string $remarks): void
    {
        $userId = Auth::id();
        $endDate = new DateTime($endDate);
        $work = $this->workRepository->firstOrFail($userId);

        $this->checkNotStartDate($work, $userId);
        $this->checkEndDate($work, $userId);
        $this->checkBreakDate($work, $userId);
        $params = [
            'user_id' => $userId,
            'end' => $endDate->format('Y-m-d H:i'),
        ];

        try {
            DB::transaction(function () use ($work, $params, $remarks) {
                $this->workRepository->update($work, $params);
                $this->storeRemarks($work, $remarks);
            });
        } catch (\Throwable $e) {
            Log::info($e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function fetchMonthDate(string $year, string $month): Collection
    {
        $userId = Auth::id();
        $targetDate = Carbon::createFromDate((int) $year, (int) $month);
        $startDate = $targetDate->startOfMonth()->format('Y-m-d');
        $endDate = $targetDate->copy()->endOfMonth()->format('Y-m-d');
        $dates = $this->dateRepository->fetchByDate($userId, $startDate, $endDate);
        $dates->map(function ($date) {
            $this->calculateSubtotal($date);
            $this->calculateTotal($date);
        });

        return $dates;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchViewDataShow(string $year, string $month, string $day): Date
    {
        $userId = Auth::id();
        $targetDate = Carbon::createFromDate((int) $year, (int) $month, (int) $day)->format('Y-m-d');
        $date = $this->dateRepository->fetchByFirstDate($userId, $targetDate);
        $this->calculateSubtotal($date);
        $this->calculateTotal($date);

        return $date;
    }

    /**
     * 日付がなければ１か月分の日付を作成
     *
     * @param bool $existsDate
     * @return void
     */
    private function checkDate(bool $existsDate): void
    {
        if (! $existsDate) {
            $currentDate = Carbon::now();
            $startDate = $currentDate->startOfMonth();
            $endDate = $currentDate->copy()->endOfMonth();
            $dates = [];
            $addDate = $startDate->copy();
            while ($addDate->lessThanOrEqualTo($endDate)) {
                $dates[] = [
                    'id' => (string) Ulid::generate(),
                    'year' => $addDate->format('Y'),
                    'date' => $addDate->format('Y-m-d'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
                $addDate = $addDate->copy()->addDay();
            }
            $this->bulkInsertDate($dates);
        }
    }

    /**
     * 日付をバルクインサート
     *
     * @param array $dates
     * @return void
     */
    private function bulkInsertDate(array $dates): void
    {
        $this->dateRepository->store($dates);
    }

    /**
     * 出勤日時がなければthrow
     *
     * @param Work $work
     * @param string $userId
     * @return void
     */
    private function checkNotStartDate(Work $work, string $userId): void
    {
        if ($work->start === null) {
            $message = '出勤してください。';
            Log::info($message, ['user_id' => $userId]);
            throw new ModelNotFoundException($message);
        }
    }

    /**
     * 退勤日時がなければthrow
     *
     * @param Work $work
     * @param string $userId
     * @return void
     */
    private function checkNotEndDate(Work $work, string $userId): void
    {
        if ($work->end === null) {
            $message = '既に出勤しています。';
            Log::info($message, ['user_id' => $userId]);
            throw new ConflictHttpException($message);
        }
    }

    /**
     * 退勤日時があればthrow
     *
     * @param Work $work
     * @param string $userId
     * @return void
     */
    private function checkEndDate(Work $work, string $userId): void
    {
        if ($work->end) {
            $message = '既に退勤しています。';
            Log::info($message, ['user_id' => $userId]);
            throw new ConflictHttpException($message);
        }
    }

    /**
     * 休憩を終了していなければthrow
     *
     * @param Work $work
     * @param string $userId
     * @return void
     */
    private function checkBreakDate(Work $work, string $userId): void
    {
        $breakTime = $this->breakTimeRepository->first($work);
        if (! $breakTime) {
            return;
        }
        if ($breakTime->break_start && ! $breakTime->break_end) {
            $message = '休憩終了してから退勤してください。';
            Log::info($message, ['user_id' => $userId]);
            throw new ConflictHttpException($message);
        }
    }

    /**
     * 備考保存
     *
     * @param Work $work
     * @param string|null $remarks
     * @return void
     */
    private function storeRemarks(Work $work, ?string $remarks): void
    {
        if ($remarks) {
            $remarksParams = [
                'work_id' => $work->id,
                'body' => $remarks,
            ];
            $this->remarksRepository->store($remarksParams);
        }
    }

    /**
     * 小計を計算
     *
     * @return void
     */
    private function calculateSubtotal(Date $date): void
    {
        $date->works->each(function ($work) {
            $startDate = new Carbon($work->start);
            $endDate = new Carbon($work->end);
            $diffInMinutes = $startDate->diffInMinutes($endDate);
            $hours = floor($diffInMinutes / 60);
            $minutes = floor(($diffInMinutes % 60));
            $work->subtotal = sprintf('%02d:%02d', $hours, $minutes);
            $work->start = $startDate->format('H:i');
            if ($work->end) {
                $work->end = $endDate->format('H:i');
            }
        });
    }

    /**
     * 合計を計算
     *
     * @param Date $date
     * @return void
     */
    private function calculateTotal(Date $date): void
    {
        // 各作業の小計（分単位）を計算
        $totalMinutes = $date->works->reduce(function ($carry, $work) {
            $parts = explode(':', $work->subtotal); // 小計を時間と分に分割
            $minutes = ($parts[0] * 60) + $parts[1]; // 時間を分に変換して合計

            return $carry + $minutes;
        }, 0);

        // 合計した分を時間と分に変換
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        // フォーマットしてログに記録
        $totalFormatted = sprintf('%02d:%02d', $hours, $minutes);
        $date->total = $totalFormatted;
    }
}
