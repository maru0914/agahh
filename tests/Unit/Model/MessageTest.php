<?php

use App\Models\Message;
use App\Models\User;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class);

it('can get a parent user', function () {
    $user = User::factory()->create();
    $message = Message::factory()->create(['user_id' => $user->id]);

    expect($message->user)
        ->toBeInstanceOf(User::class)
        ->id->toBe($user->id);
});
