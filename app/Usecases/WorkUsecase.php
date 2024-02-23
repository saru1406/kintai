<?php

declare(strict_types=1);

namespace App\Usecases;

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
        $dateTime = new DateTime($startDate);
        $userId = Auth::id();
        $this->exists($dateTime->format('Y-m-d'), $userId);
        $params = [
            'user_id' => $userId,
            'start_date' => $dateTime->format('Y-m-d'),
            'start' => $dateTime->format('Y-m-d H:i:s'),
        ];
        $this->workRepository->store($params);
    }

    /**
     * 同じ日付があればthrow
     *
     * @param string $startDate
     * @param int $userId
     * @return void
     */
    private function exists(string $startDate, int $userId): void
    {
        $checkDate = $this->workRepository->exists($startDate);
        if ($checkDate === true) {
            $message = '既に出勤しています。';
            Log::info($message, ['user_id' => $userId]);
            throw new ConflictHttpException($message);
        }
    }
}
