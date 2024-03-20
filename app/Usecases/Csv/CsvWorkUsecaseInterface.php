<?php

declare(strict_types=1);

namespace App\Usecases\Csv;

use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface CsvWorkUsecaseInterface
{
    public function exportCsv(Collection $dates): StreamedResponse;
}
