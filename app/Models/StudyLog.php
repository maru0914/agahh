<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property Carbon $studied_at
 * @property Carbon $started_at
 * @property Carbon $ended_at
 */
class StudyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'studied_at',
        'subject',
        'duration_minutes',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'studied_at' => 'date:Y-m-d',
            'started_at' => 'datetime:Y-m-d H:i',
            'ended_at' => 'datetime:Y-m-d H:i',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
