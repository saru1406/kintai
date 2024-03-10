<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Work extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'date_id',
        'start',
        'end',
    ];

    /**
     * ユーザに紐づけ
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 日付と紐づけ
     *
     * @return BelongsTo
     */
    public function date(): BelongsTo
    {
        return $this->belongsTo(Date::class);
    }

    /**
     * 休憩と紐づけ
     *
     * @return HasMany
     */
    public function breakTimes(): HasMany
    {
        return $this->hasMany(BreakTime::class);
    }
}
