<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Work;

class WorkRepository implements WorkRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function store(array $params): void
    {
        Work::create($params);
    }
}
