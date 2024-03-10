<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FetchWorkDateApiRequest;
use App\Http\Requests\WorkEndApiRequest;
use App\Http\Requests\WorkStartApiRequest;
use App\Usecases\Work\WorkUsecaseInterface;
use Illuminate\Http\JsonResponse;

class WorkController extends Controller
{
    public function __construct(private WorkUsecaseInterface $workUsecase)
    {
    }

    /**
     * 勤怠開始
     *
     * @param WorkStartApiRequest $request
     * @return JsonResponse
     */
    public function start(WorkStartApiRequest $request): JsonResponse
    {
        $this->workUsecase->storeStart($request->getStartDate(), $request->getRemarks());

        return response()->json(['message' => '出勤日時を保存しました']);
    }

    /**
     * 勤怠終了
     *
     * @param WorkEndApiRequest $request
     * @return JsonResponse
     */
    public function end(WorkEndApiRequest $request): JsonResponse
    {
        $this->workUsecase->storeEnd($request->getEndDate(), $request->getRemarks());

        return response()->json(['message' => '退勤日時を保存しました']);
    }

    /**
     * 月毎の勤怠を取得
     *
     * @param FetchWorkDateApiRequest $request
     * @return JsonResponse
     */
    public function fetch(FetchWorkDateApiRequest $request): JsonResponse
    {
        $data = $this->workUsecase->fetchMonthDate($request->getYear(), $request->getMonth());

        return response()->json([
            'work_data' => $data,
            'message' => '取得しました。',
        ]);
    }
}
