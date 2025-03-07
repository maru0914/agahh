<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Carbon;

class GeneratePreviousDiaryLink
{
    /**
     * Execute the action.
     */
    public function handle(User $user, Carbon $date): string
    {
        $previousDate = $date->subDay();

        $previousDiary = $user->diaries()
            ->whereDate('date', $previousDate)
            ->first();

        if ($previousDiary) {
            return route('diaries.show', $previousDiary->date->format('Y-m-d'));
        }

        return route('diaries.create', ['date' => $previousDate->format('Y-m-d')]);
    }
}
