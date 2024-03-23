<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CsvWorkApiRequest;
use App\Usecases\Csv\CsvWorkUsecaseInterface;
use App\Usecases\Work\WorkUsecaseInterface;

class CsvWorkController extends Controller
{
    public function __construct(
        private readonly WorkUsecaseInterface $workUsecase,
        private readonly CsvWorkUsecaseInterface $csvWorkUsecase
    ) {
    }

    public function __invoke(CsvWorkApiRequest $request)
    {
        $dates = $this->workUsecase->fetchMonthDate($request->getYear(), $request->getMonth());

        return $this->csvWorkUsecase->exportCsv($dates);
    }
}
