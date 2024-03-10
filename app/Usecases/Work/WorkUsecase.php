<?php

declare(strict_types=1);

namespace App\Usecases\Work;

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
            'start' => $startDateTime->format('Y-m-d H:i:s'),
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
            'end' => $endDate->format('Y-m-d H:i:s'),
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
        $targetDate = Carbon::createFromDate((int)$year, (int)$month);
        $startDate = $targetDate->startOfMonth()->format('Y-m-d');
        $endDate = $targetDate->copy()->endOfMonth()->format('Y-m-d');
        return $this->dateRepository->fetchByDate($userId, $startDate, $endDate);
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
}
