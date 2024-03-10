<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BreakTime extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'work_id',
        'break_start',
        'break_end',
        'is_break_status',
    ];

    /**
     * 勤怠と紐づけ
     *
     * @return BelongsTo
     */
    public function work(): BelongsTo
    {
        return $this->belongsTo(Work::class);
    }
}
