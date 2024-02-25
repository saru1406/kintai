<?php

declare(strict_types=1);

namespace App\Repositories\BreakTime;

use App\Models\BreakTime;
use App\Models\Work;

class BreakTimeRepository implements BreakTimeRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function store(array $params): void
    {
        BreakTime::create($params);
    }

    /**
     * {@inheritDoc}
     */
    public function update(BreakTime $breakTime, array $params): void
    {
        $breakTime->update($params);
    }

    /**
     * {@inheritDoc}
     */
    public function first(Work $work): ?BreakTime
    {
        return BreakTime::where('work_id', $work->id)->orderBy('created_at', 'desc')->first();
    }
}
