<?php

use App\Actions\GenerateNextDiaryLink;
use App\Models\Diary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class);

it('returns null if the date is today', function () {
    $user = User::factory()->create();
    $today = Carbon::today();

    $action = new GenerateNextDiaryLink;
    $result = $action->handle($user, $today);

    expect($result)->toBeNull();
});

it('returns the show route if a next diary exists', function () {
    $this->freezeTime(function () {
        $user = User::factory()->create();

        $today = today();

        Diary::factory()->for($user)->create([
            'date' => $today->format('Y-m-d'),
        ]);

        $action = new GenerateNextDiaryLink;
        $result = $action->handle($user, Carbon::yesterday());
        expect($result)->toBe(route('diaries.show', $today->format('Y-m-d')));
    });
});

it('returns the create route if no next diary exists', function () {
    $this->freezeTime(function () {
        $user = User::factory()->create();

        $action = new GenerateNextDiaryLink;
        $result = $action->handle($user, Carbon::yesterday());

        expect($result)->toBe(route('diaries.create', ['date' => today()->format('Y-m-d')]));
    });
});
