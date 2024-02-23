<?php

declare(strict_types=1);

namespace App\Usecases;

interface WorkUsecaseInterface
{
    /**
     * 出勤日時保存
     *
     * @param string $startDate
     * @return void
     */
    public function storeStart(string $startDate): void;
}
