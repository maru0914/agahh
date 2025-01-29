<?php

use App\Models\User;
use Illuminate\Support\Facades\Http;

test('can chat', function () {
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

    $response = $this
        ->actingAs($user)
        ->post('/api/chat', [
            'content' => 'Hello, World!',
        ]);

    $response->assertOk();
});
