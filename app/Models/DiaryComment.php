<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryComment extends Model
{
    /** @use HasFactory<\Database\Factories\DiaryCommentFactory> */
    use HasFactory;

    protected $fillable = [
        'diary_id',
        'content',
    ];

    /** @return BelongsTo<Diary, $this> */
    public function diary(): BelongsTo
    {
        return $this->belongsTo(Diary::class);
    }
}
