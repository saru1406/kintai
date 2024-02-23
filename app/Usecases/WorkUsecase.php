<?php

declare(strict_types=1);

namespace App\Usecases;

use App\Repositories\WorkRepositoryInterface;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkUsecase implements WorkUsecaseInterface
{
    public function __construct(private WorkRepositoryInterface $workRepository)
    {
    }

    public function storeStart(string $startDate): void
    {
        $startDateFormat = (new DateTime($startDate))->format('Y-m-d H:i:s');
        $user_id = Auth::id();
        $params = [
            'user_id' => $user_id,
            'start' => $startDateFormat
        ];
        $this->workRepository->store($params);
    }
}
