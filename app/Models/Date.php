<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Date extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'id',
        'year',
        'date',
    ];

    /**
     * 取得時dateカラムをフォーマット
     *
     * @param string $value
     * @return string
     */
    public function getDateAttribute(string $value): string
    {
        return Carbon::parse($value)->format('m月d日');
    }

    /**
     * 勤怠と紐づけ
     *
     * @return HasMany
     */
    public function works(): HasMany
    {
        return $this->hasMany(Work::class);
    }
}
