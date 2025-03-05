<?php

namespace App\Policies;

use App\Models\Diary;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DiaryPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Diary $diary): Response
    {
        return $user->id === $diary->user_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, string $date): Response
    {
        $existingDiary = Diary::where('date', $date)
            ->whereBelongsTo($user)
            ->first();

        $isTodayOrBefore = $date <= now()->format('Y-m-d');

        return ! $existingDiary && $isTodayOrBefore
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Diary $diary): Response
    {
        return $user->id === $diary->user_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Diary $diary): Response
    {
        return $user->id === $diary->user_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
