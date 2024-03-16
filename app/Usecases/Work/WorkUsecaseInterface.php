<?php

declare(strict_types=1);

namespace App\Usecases\Work;

use App\Models\Date;
use Illuminate\Support\Collection;

interface WorkUsecaseInterface
{
    /**
     * 出勤日時保存
     *
     * @param string $startDate
     * @param string|null $remarks
     * @return void
     */
    public function storeStart(string $startDate, ?string $remarks): void;

    /**
     * 退勤日時保存
     *
     * @param string $endDate
     * @param string|null $remarks
     * @return void
     */
    public function storeEnd(string $endDate, ?string $remarks): void;

    /**
     * 指定月毎のデータを取得
     *
     * @param string $year
     * @param string $month
     * @return Collection
     */
    public function fetchMonthDate(string $year, string $month): Collection;

    /**
     * show画面データを取得
     *
     * @param string $year
     * @param string $month
     * @param string $day
     * @return Date
     */
    public function fetchViewDataShow(string $year, string $month, string $day): Date;
}
