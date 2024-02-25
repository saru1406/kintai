<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkBreakEndApiRequest;
use App\Http\Requests\WorkBreakStartApiRequest;
use App\Usecases\BreakTime\BreakTimeUsecaseInterface;

class BreakTimeController extends Controller
{
    public function __construct(private BreakTimeUsecaseInterface $breakTimeUsecase)
    {
    }

    public function breakStart(WorkBreakStartApiRequest $request)
    {
        $this->breakTimeUsecase->storeBreakStart($request->getBreakStart(), $request->getRemarks());

        return response()->json(['message' => '休憩を開始しました。']);
    }

    public function breakEnd(WorkBreakEndApiRequest $request)
    {
        $this->breakTimeUsecase->storeBreakEnd($request->getBreakEnd(), $request->getRemarks());

        return response()->json(['message' => '休憩を終了しました。']);
    }

    public function fetchBreakStatus()
    {
        $breakStatus = $this->breakTimeUsecase->fetchBreakStatus();

        return response()->json(['break_status' => $breakStatus]);
    }
}
