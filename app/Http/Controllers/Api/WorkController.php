<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkEndApiRequest;
use App\Http\Requests\WorkStartApiRequest;
use App\Usecases\WorkUsecaseInterface;

class WorkController extends Controller
{
    public function __construct(private WorkUsecaseInterface $workUsecase)
    {
    }

    public function start(WorkStartApiRequest $request)
    {
        $this->workUsecase->storeStart($request->getStartDate(), $request->getRemarks());

        return response()->json(['message' => '出勤日時を保存しました']);
    }

    public function end(WorkEndApiRequest $request)
    {
        $this->workUsecase->storeEnd($request->getEndDate(), $request->getRemarks());

        return response()->json(['message' => '退勤日時を保存しました']);
    }
}
