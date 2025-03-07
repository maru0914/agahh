<?php

use App\Actions\GeneratePreviousDiaryLink;
use App\Models\Diary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class);

it('returns the show route if a previous diary exists', function () {
    $this->freezeTime(function () {
        $user = User::factory()->create();
        $yesterday = Carbon::yesterday();

        Diary::factory()->for($user)->create([
            'date' => $yesterday->format('Y-m-d'),
        ]);

        $action = new GeneratePreviousDiaryLink;
        $result = $action->handle($user, today());

        expect($result)->toBe(route('diaries.show', $yesterday->format('Y-m-d')));
    });
});

it('returns the create route if no previous diary exists', function () {
    $this->freezeTime(function () {
        $user = User::factory()->create();

        $action = new GeneratePreviousDiaryLink;
        $result = $action->handle($user, today());

        expect($result)->toBe(route('diaries.create', [
            'date' => Carbon::yesterday()->format('Y-m-d'),
        ]));
    });
});
