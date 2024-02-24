<?php

declare(strict_types=1);

namespace App\Repositories\Work;

use App\Models\Work;

class WorkRepository implements WorkRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function store(array $params): Work
    {
        return Work::create($params);
    }

    /**
     * {@inheritDoc}
     */
    public function update(Work $work, array $params): void
    {
        $work->update($params);
    }

    /**
     * {@inheritDoc}
     */
    public function firstOrFail(int $userId): Work
    {
        return Work::where('user_id', $userId)->orderBy('created_at', 'desc')->firstOrFail();
    }

    /**
     * {@inheritDoc}
     */
    public function exists(int $userId): bool
    {
        return Work::where('user_id', $userId)->orderBy('created_at', 'desc')->exists();
    }
}
