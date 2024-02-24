<?php

declare(strict_types=1);

namespace App\Repositories\Work;

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
     * 勤怠更新
     *
     * @param Work $work
     * @param array $params
     * @return void
     */
    public function update(Work $work, array $params): void;

    /**
     * 最新のレコード取得
     *
     * @param int $userId
     * @return Work
     */
    public function firstOrFail(int $userId): Work;

    /**
     * レコードの存在確認
     *
     * @param int $userId
     * @return bool
     */
    public function exists(int $userId): bool;
}
