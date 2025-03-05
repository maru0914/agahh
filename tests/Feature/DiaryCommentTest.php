<?php

use App\Models\Diary;
use App\Models\DiaryComment;
use App\Models\User;
use Illuminate\Support\Facades\Http;

it('can store diary comment', function () {
    $this->travelTo(now(), function () {
        Http::fake([
            'api.openai.com/*' => '
                {
                    "choices": [
                        {
                            "message": {
                                "role": "assistant",
                                "content": "Hello from assistant"
                            }
                        }
                    ]
                }
          ',
        ]);

        $user = User::factory()->create();

        $pastDiary = Diary::factory()->for($user)->create([
            'date' => now()->subDay(),
        ]);
        $targetDiary = Diary::factory()->for($user)->create([
            'date' => now(),
        ]);

        $response = $this->actingAs($user)
            ->post(route('diaries.comments.store', ['diary' => $targetDiary->date->format('Y-m-d')]), [
                'content' => 'Hello from user',
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('diary_comments', [
            'content' => 'Hello from assistant',
        ]);
    });
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
