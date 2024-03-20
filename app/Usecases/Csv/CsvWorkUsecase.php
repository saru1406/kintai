<?php

declare(strict_types=1);

namespace App\Usecases\Csv;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvWorkUsecase implements CsvWorkUsecaseInterface
{
    public function exportCsv(Collection $dates): StreamedResponse
    {
        $userId = Auth::id();
        $csvHeader = ['日付', '開始時間', '終了時間', '休憩開始', '休憩終了', '小計'];
        $response = new StreamedResponse(function () use ($csvHeader, $dates) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $csvHeader);

            foreach ($dates as $date) {
                $rowData = [
                    $date->date,
                    $date->works[0]->start ?? '',
                    $date->works[0]->end ?? '',
                    $date->works[0]->breakTimes[0]->breakStart ?? '',
                    $date->works[0]->breakTimes[0]->breakEnd ?? '',
                    $date->total === '00:00' ? '' : $date->total,
                ];
                fputcsv($handle, $rowData);
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users.csv"',
        ]);

        return $response;
    }
}
