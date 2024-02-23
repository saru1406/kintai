<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Work;

interface WorkRepositoryInterface
{
    /**
     * 出勤保存
     *
     * @param array $params
     * @return Work
     */
    public function store(array $params): Work;

    /**
     * 退勤保存
     *
     * @param Work $work
     * @param array $params
     * @return void
     */
    public function updateEnd(Work $work, array $params): void;

    /**
     * 最新のレコード取得
     *
     * @param int $userId
     * @return Work
     */
    public function firstOrFail(int $userId): Work;

    /**
     * 出勤存在確認
     *
     * @param int $userId
     * @param string $startDate
     * @return bool
     */
    public function existsStartDate(int $userId, string $startDate): bool;
}
