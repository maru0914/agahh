<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\DiaryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property Carbon $date
 */
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

    /** @return HasOne<DiaryComment, $this> */
    public function comment(): HasOne
    {
        return $this->hasOne(DiaryComment::class, 'diary_id');
    }

    /** @return BelongsTo<User, $this> */
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
            $date->startOfMonth()->toDateString(),
            $date->endOfMonth()->toDateString(),
        ]);
    }
}
