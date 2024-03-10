<?php

declare(strict_types=1);

namespace App\Repositories\Date;

use App\Models\Date;
use Illuminate\Support\Collection;

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

    /**
     * {@inheritDoc}
     */
    public function fetchByDate(string $userId, string $startDate, string $endDate): Collection
    {
        return Date::where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->with(['works' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }, 'works.breakTimes'])->get();
    }
}
