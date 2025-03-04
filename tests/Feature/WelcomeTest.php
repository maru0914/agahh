<?php

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;

it('returns a successful response', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/');

    $response->assertStatus(200);
});

it('show messages in today', function () {
    $this->travelTo(Carbon::createFromFormat('Y-m-d', '2025-01-01'), function () {
        $user = User::factory()->create();
        Message::factory()
            ->for($user)
            ->count(5)
            ->sequence(
                ['created_at' => Carbon::createFromFormat('Y-m-d H:i:s', '2024-12-31 23:59:59')],
                ['created_at' => Carbon::createFromFormat('Y-m-d H:i:s', '2025-01-01 00:00:00')],
                ['created_at' => Carbon::createFromFormat('Y-m-d H:i:s', '2025-01-01 12:00:00')],
                ['created_at' => Carbon::createFromFormat('Y-m-d H:i:s', '2025-01-01 23:59:59')],
                ['created_at' => Carbon::createFromFormat('Y-m-d H:i:s', '2025-01-02 00:00:01')],
            )
            ->create();
        $response = $this->actingAs($user)->get('/');
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('messagesInToday', 3));
    });
});
