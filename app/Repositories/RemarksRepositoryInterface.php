<?php

declare(strict_types=1);

namespace App\Repositories;

interface RemarksRepositoryInterface
{
    /**
     * 備考保存
     *
     * @param array $params
     * @return void
     */
    public function store(array $params): void;
}
