<?php

declare(strict_types=1);

namespace App\Usecases\BreakTime;

use App\Models\BreakTime;
use App\Models\Work;
use App\Repositories\BreakTime\BreakTimeRepositoryInterface;
use App\Repositories\Remarks\RemarksRepositoryInterface;
use App\Repositories\Work\WorkRepositoryInterface;
use DateTime;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class BreakTimeUsecase implements BreakTimeUsecaseInterface
{
    public function __construct(
        private WorkRepositoryInterface $workRepository,
        private RemarksRepositoryInterface $remarksRepository,
        private BreakTimeRepositoryInterface $breakTimeRepository,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function storeBreakStart(string $beakStart, ?string $remarks): void
    {
        $userId = Auth::id();
        $breakStartDate = new DateTime($beakStart);
        $work = $this->workRepository->firstOrFail($userId);

        $this->checkBreakStart($work, $userId);
        $this->checkEndDate($work, $userId);
        $params = [
            'work_id' => $work->id,
            'break_start' => $breakStartDate->format('Y-m-d H:i:s'),
            'is_break_status' => true,
        ];

        try {
            DB::transaction(function () use ($work, $params, $remarks) {
                $this->breakTimeRepository->store($params);
                $this->storeRemarks($work, $remarks);
            });
        } catch (\Throwable $e) {
            Log::info($e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function storeBreakEnd(string $breakEnd, ?string $remarks): void
    {
        $userId = Auth::id();
        $breakEndDate = new DateTime($breakEnd);
        $work = $this->workRepository->firstOrFail($userId);
        $breakTime = $this->breakTimeRepository->first($work);

        $this->checkBreakDate($breakTime, $userId);
        $this->checkEndDate($work, $userId);
        $this->checkNotBreakStart($breakTime, $userId);
        $this->checkBreakEnd($breakTime, $userId);

        $params = [
            'break_end' => $breakEndDate->format('Y-m-d H:i:s'),
            'is_break_status' => false,
        ];

        try {
            DB::transaction(function () use ($work, $breakTime, $params, $remarks) {
                $this->breakTimeRepository->update($breakTime, $params);
                $this->storeRemarks($work, $remarks);
            });
        } catch (\Throwable $e) {
            Log::info($e);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function fetchBreakStatus(): bool
    {
        try {
            $work = $this->workRepository->firstOrFail(Auth::id());
        } catch (\Throwable $e) {
            Log::info($e);
            return false;
        }

        $breakTime = $this->breakTimeRepository->first($work);
        if (!$breakTime) {
            return false;
        }

        return (bool) $breakTime->is_break_status;
    }

    /**
     * 休憩を開始していなければthrow
     *
     * @param Work $work
     * @param string $userId
     * @return void
     */
    private function checkBreakDate(BreakTime $breakTime, string $userId): void
    {
        if (!$breakTime) {
            $message = '休憩を開始してください。';
            Log::info($message, ['user_id' => $userId]);
            throw new ModelNotFoundException($message);
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
     * 休憩開始日時があればthrow
     *
     * @param Work $work
     * @param string $userId
     * @return void
     */
    private function checkBreakStart(Work $work, string $userId): void
    {
        if ($work->break_start) {
            $message = '既に休憩開始しています。';
            Log::info($message, ['user_id' => $userId]);
            throw new ConflictHttpException($message);
        }
    }

    /**
     * 休憩開始日時がなければthrow
     *
     * @param BreakTIme $work
     * @param string $userId
     * @return void
     */
    private function checkNotBreakStart(BreakTime $breakTIme, string $userId): void
    {
        if ($breakTIme->break_start === null) {
            $message = '休憩が開始されていません。';
            Log::info($message, ['user_id' => $userId]);
            throw new ModelNotFoundException($message);
        }
    }

    /**
     * 休憩終了日時があればthrow
     *
     * @param BreakTime $breakTime
     * @param string $userId
     * @return void
     */
    private function checkBreakEnd(BreakTime $breakTime, string $userId): void
    {
        if ($breakTime->break_end) {
            $message = '休憩時間を開始してください。';
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
