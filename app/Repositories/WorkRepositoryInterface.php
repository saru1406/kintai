<?php

declare(strict_types=1);

namespace App\Repositories;

interface WorkRepositoryInterface
{
    /**
     * 勤怠保存
     *
     * @param array $params
     * @return void
     */
    public function store(array $params): void;

    /**
     * 勤怠存在確認
     *
     * @param string $startDate
     * @return boolean
     */
    public function exists(string $startDate): bool;
}
