<?php

declare(strict_types=1);

namespace App\Repositories\Date;

use App\Models\Date;

class DateRepository implements DateRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function store(array $params): void
    {
        Date::insert($params);
    }

    /**
     * {@inheritDoc}
     */
    public function existsByDate(string $date): bool
    {
        return Date::where('date', $date)->exists();
    }

    /**
     * {@inheritDoc}
     */
    public function firstOrFailByCurrentDate(string $date): Date
    {
        return Date::where('date', $date)->firstOrFail();
    }
}
