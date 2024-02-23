<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkStartApiRequest;
use App\Usecases\WorkUsecaseInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WorkController extends Controller
{
    public function __construct(private WorkUsecaseInterface $workUsecase)
    {
    }

    public function start(WorkStartApiRequest $request)
    {
        $this->workUsecase->storeStart($request->getStartDate());
    }
}