<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkBreakEndApiRequest;
use App\Http\Requests\WorkBreakStartApiRequest;
use App\Usecases\BreakTime\BreakTimeUsecaseInterface;
use Illuminate\Http\JsonResponse;

class BreakTimeController extends Controller
{
    public function __construct(private BreakTimeUsecaseInterface $breakTimeUsecase)
    {
    }

    /**
     * 休憩開始
     *
     * @param WorkBreakStartApiRequest $request
     * @return JsonResponse
     */
    public function breakStart(WorkBreakStartApiRequest $request): JsonResponse
    {
        $this->breakTimeUsecase->storeBreakStart($request->getBreakStart(), $request->getRemarks());

        return response()->json(['message' => '休憩を開始しました。']);
    }

    /**
     * 休憩終了
     *
     * @param WorkBreakEndApiRequest $request
     * @return JsonResponse
     */
    public function breakEnd(WorkBreakEndApiRequest $request): JsonResponse
    {
        $this->breakTimeUsecase->storeBreakEnd($request->getBreakEnd(), $request->getRemarks());

        return response()->json(['message' => '休憩を終了しました。']);
    }

    /**
     * 休憩の有無を取得
     *
     * @return JsonResponse
     */
    public function fetchBreakStatus(): JsonResponse
    {
        $breakStatus = $this->breakTimeUsecase->fetchBreakStatus();

        return response()->json(['break_status' => $breakStatus]);
    }
}
