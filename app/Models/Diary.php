<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\DiaryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Diary extends Model
{
    /** @use HasFactory<DiaryFactory> */
    use HasFactory;

    protected $fillable = ['date', 'content', 'user_id'];

    protected function casts(): array
    {
        return [
            'date' => 'datetime:Y-m-d',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include diaries of the same month.
     */
    public function scopeSameMonth(Builder $query, CarbonImmutable|Carbon $date): void
    {
        $query->whereBetween('date', [
            $date->startOfMonth(),
            $date->endOfMonth(),
        ]);
    }
}
