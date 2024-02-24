<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkBreakEndApiRequest;
use App\Http\Requests\WorkBreakStartApiRequest;
use App\Http\Requests\WorkEndApiRequest;
use App\Http\Requests\WorkStartApiRequest;
use App\Usecases\Work\WorkUsecaseInterface;

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

    public function breakStart(WorkBreakStartApiRequest $request)
    {
        $this->workUsecase->storeBreakStart($request->getBreakStart(), $request->getRemarks());

        return response()->json(['message' => '休憩を開始しました。']);
    }

    public function breakEnd(WorkBreakEndApiRequest $request)
    {
        $this->workUsecase->storeBreakEnd($request->getBreakEnd(), $request->getRemarks());

        return response()->json(['message' => '休憩を終了しました。']);
    }

    public function fetchBreakStatus()
    {
        $breakStatus = $this->workUsecase->fetchBreakStatus();

        return response()->json(['break_status' => $breakStatus]);
    }
}
