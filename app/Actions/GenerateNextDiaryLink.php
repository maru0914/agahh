<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Carbon;

class GenerateNextDiaryLink
{
    /**
     * Execute the action.
     */
    public function handle(User $user, Carbon $date): ?string
    {
        if ($date->isToday()) {
            return null;
        }
        $nextDate = $date->addDay();

        $nextDiary = $user->diaries()
            ->whereDate('date', $nextDate)
            ->first();

        if ($nextDiary) {
            return route('diaries.show', $nextDiary->date->format('Y-m-d'));
        }

        return route('diaries.create', ['date' => $nextDate->format('Y-m-d')]);
    }
}
