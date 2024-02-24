<?php

declare(strict_types=1);

namespace App\Repositories\Remarks;

use App\Models\Remarks;

class RemarksRepository implements RemarksRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function store(array $params): void
    {
        Remarks::create($params);
    }
}
