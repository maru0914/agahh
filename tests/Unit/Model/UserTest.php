<?php

use App\Models\Diary;
use App\Models\User;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can get a child diaries', function () {
    $user = User::factory()->create();
    $diaries = Diary::factory()
        ->count(2)
        ->for($user)
        ->sequence(
            ['date' => '2021-01-01'],
            ['date' => '2021-01-02']
        )
        ->create();

    expect($user->diaries->first())
        ->toBeInstanceOf(Diary::class)
        ->and($user->diaries->count())
        ->toBe(2);
});
