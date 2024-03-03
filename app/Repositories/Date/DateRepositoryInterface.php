<?php

declare(strict_types=1);

namespace App\Repositories\Date;

use App\Models\Date;

interface DateRepositoryInterface
{
    /**
     * 月の日付を一括保存
     *
     * @param array $params
     * @return void
     */
    public function store(array $params): void;

    /**
     * 日付があるか存在確認
     *
     * @param string $date
     * @return bool
     */
    public function existsByDate(string $date): bool;

    /**
     * 現在の日付のレコードを取得
     *
     * @param string $date
     * @return Date
     */
    public function firstOrFailByCurrentDate(string $date): Date;
}
