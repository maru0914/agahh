<?php

use App\Models\Diary;
use App\Models\User;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can get a child diaries', function () {
    $user = User::factory()->create();
    $diaries = Diary::factory(2)->create(['user_id' => $user->id]);

    expect($user->diaries->first())
        ->toBeInstanceOf(Diary::class)
        ->and($user->diaries->count())
        ->toBe(2);
});
