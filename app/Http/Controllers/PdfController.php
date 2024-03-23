<?php

namespace App\Http\Controllers;

use App\Http\Requests\PdfWorkApiRequest;
use App\Services\Pdf\PdfServiceInterface;
use App\Usecases\Work\WorkUsecaseInterface;

class PdfController extends Controller
{
    public function __construct(
        private readonly WorkUsecaseInterface $workUsecase,
        private readonly PdfServiceInterface $pdfService
    ) {
    }

    public function __invoke(PdfWorkApiRequest $request)
    {
        $dates = $this->workUsecase->fetchMonthDate($request->getYear(), $request->getMonth());
        $data = $this->pdfService->pdfData($dates);
    }
}
