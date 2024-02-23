<?php

declare(strict_types=1);

namespace App\Usecases;

use App\Models\Work;
use App\Repositories\WorkRepositoryInterface;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class WorkUsecase implements WorkUsecaseInterface
{
    public function __construct(private WorkRepositoryInterface $workRepository)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function storeStart(string $startDate): void
    {
        $startDateTime = new DateTime($startDate);
        $userId = Auth::id();
        $this->existsStartDate($startDateTime->format('Y-m-d'), $userId);
        $params = [
            'user_id' => $userId,
            'start' => $startDateTime->format('Y-m-d H:i:s'),
        ];
        $this->workRepository->store($params);
    }

    /**
     * {@inheritDoc}
     */
    public function storeEnd(string $endDate): void
    {
        $userId = Auth::id();
        $endDate = new DateTime($endDate);
        $work = $this->workRepository->firstOrFail($userId);
        $this->checkEndDate($work, $userId);
        $params = [
            'user_id' => $userId,
            'end' => $endDate->format('Y-m-d H:i:s'),
        ];
        $this->workRepository->updateEnd($work, $params);
    }

    /**
     * 出勤日時に同じ日付があればthrow
     *
     * @param string $startDate
     * @param int $userId
     * @return void
     */
    private function existsStartDate(string $startDate, int $userId): void
    {
        $checkDate = $this->workRepository->existsStartDate($userId, $startDate);
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
}
