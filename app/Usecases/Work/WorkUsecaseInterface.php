<?php

declare(strict_types=1);

namespace App\Usecases\Work;

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
     * 休憩開始日時保存
     *
     * @param string $beakStart
     * @param string|null $remarks
     * @return void
     */
    public function storeBreakStart(string $beakStart, ?string $remarks): void;
}