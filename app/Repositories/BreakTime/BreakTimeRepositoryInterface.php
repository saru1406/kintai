<?php

declare(strict_types=1);

namespace App\Repositories\BreakTime;

use App\Models\BreakTime;
use App\Models\Work;

interface BreakTimeRepositoryInterface
{
    /**
     * 休憩時間保存
     *
     * @param array $params
     * @return void
     */
    public function store(array $params): void;

    /**
     * 休憩時間更新
     *
     * @param BreakTime $breakTime
     * @param array $params
     * @return void
     */
    public function update(BreakTime $breakTime, array $params): void;

    /**
     * 休憩時間取得
     *
     * @param Work $work
     * @return BreakTime|null
     */
    public function first(Work $work): ?BreakTime;
}
