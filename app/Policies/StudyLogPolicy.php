<?php

namespace App\Policies;

use App\Models\StudyLog;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class StudyLogPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StudyLog $studyLog): Response
    {
        return $user->id === $studyLog->user_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StudyLog $studyLog): Response
    {
        return $user->id === $studyLog->user_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StudyLog $studyLog): Response
    {
        return $user->id === $studyLog->user_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
