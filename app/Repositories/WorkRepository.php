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

    /**
     * {@inheritDoc}
     */
    public function exists(string $startDate): bool
    {
        return Work::where('start_date', $startDate)->exists();
    }
}
