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
}
