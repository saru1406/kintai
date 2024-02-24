<?php

declare(strict_types=1);

namespace App\Usecases\Work;

use App\Models\Work;
use App\Repositories\Remarks\RemarksRepositoryInterface;
use App\Repositories\Work\WorkRepositoryInterface;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class WorkUsecase implements WorkUsecaseInterface
{
    public function __construct(
        private WorkRepositoryInterface $workRepository,
        private RemarksRepositoryInterface $remarksRepository,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function storeStart(string $startDate, ?string $remarks): void
    {
        $startDateTime = new DateTime($startDate);
        $userId = Auth::id();
        $existsParams = ['start' => $startDateTime->format('Y-m-d')];
        $this->checkStartDate($existsParams, $userId);
        $params = [
            'user_id' => $userId,
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
        $this->checkEndDate($work, $userId);
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
    public function storeBreakStart(string $beakStart, ?string $remarks): void
    {
        $userId = Auth::id();
        $breakStartDate = new DateTime($beakStart);
        $existsParams = ['break_start' => $breakStartDate->format('Y-m-d')];
        $work = $this->workRepository->firstOrFail($userId);
        $this->checkEndDate($work, $userId);
        $this->checkStartDate($existsParams, $userId);
        $params = [
            'user_id' => $userId,
            'break_start' => $breakStartDate->format('Y-m-d H:i:s'),
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
     * 同じ日付があればthrow
     *
     * @param array $startDate
     * @param int $userId
     * @return void
     */
    private function checkStartDate(array $params, int $userId): void
    {
        $checkDate = $this->workRepository->exists($userId, $params);
        if ($checkDate === true) {
            $message = '既に出勤しています。';
            Log::info($message, ['user_id' => $userId]);
            throw new ConflictHttpException($message);
        }
    }

    /**
     * 退勤日時があればthrow
     *
     * @param Work $work
     * @param int $userId
     * @return void
     */
    private function checkEndDate(Work $work, int $userId): void
    {
        if ($work->end) {
            $message = '既に退勤しています。';
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
