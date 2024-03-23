<?php

declare(strict_types=1);

namespace App\Services\Pdf;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use PDF;

class PdfService implements PdfServiceInterface
{
    /**
     * {@inheritDoc}
     */
    public function pdfData(Collection $dates): PDF
    {
        $user = Auth::user();
        $data = [
            'user' => $user,
            '$dates' => $dates,
        ];
        $pdf = PDF::loadView('pdf.test', $data);

        return $pdf;
    }
}
