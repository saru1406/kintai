<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Remarks extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id',
        'body',
    ];

    public function works(): HasMany
    {
        return $this->hasMany(Work::class);
    }
}
