<?php

declare(strict_types=1);

namespace App\Services\Pdf;

use Illuminate\Support\Collection;
use PDF;

interface PdfServiceInterface
{
    /**
     * PDFデータ
     *
     * @param Collection $dates
     * @return PDF
     */
    public function pdfData(Collection $dates): PDF;
}
