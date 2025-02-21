<?php

use App\Models\Diary;
use App\Models\DiaryComment;
use App\Models\User;
use Illuminate\Support\Facades\Http;

it('can store diary comment', function () {
    Http::fake([
        'api.openai.com/*' => '
        {
            "choices": [
                {
                    "message": {
                        "role": "assistant",
                        "content": "Hello"
                    }
                }
            ]
        }
  ',
    ]);

    $user = User::factory()->create();
    $diary = Diary::factory()->for($user)->create();

    $response = $this->actingAs($user)
        ->post(route('diaries.comments.store', ['diary' => $diary->date->format('Y-m-d')]), [
            'content' => 'Hello',
        ]);

    $response->assertStatus(302);

    $this->assertDatabaseHas('diary_comments', [
        'content' => 'Hello',
    ]);
});

it('cannot store duplicate diary comment', function () {
    $user = User::factory()->create();
    $diary = Diary::factory()->for($user)->create();
    DiaryComment::factory()->for($diary)->create(['content' => 'Hello']);

    $response = $this->actingAs($user)
        ->post(route('diaries.comments.store', ['diary' => $diary->date->format('Y-m-d')]), [
            'content' => 'Hello',
        ]);

    $response->assertStatus(302);

    $this->assertDatabaseCount('diary_comments', 1);
});
