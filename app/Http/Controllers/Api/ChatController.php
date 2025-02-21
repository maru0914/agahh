<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    /**
     * @throws ConnectionException
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);
        $request->user()->messages()->create([
            'role' => 'user',
            'content' => $request->input('content'),
        ]);

        $previousMessages = $request->user()->messages()->oldest()->get()
            ->map(
                fn ($message) => [
                    'role' => $message->role,
                    'content' => $message->content,
                ]
            );

        $response = Http::withToken(config('services.openai.key'))
            ->post(
                'https://api.openai.com/v1/chat/completions',
                [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => <<<'EOT'
                            あなたはアガーという恐竜のキャラクター(人形)です。
                            5才で少しぶっきらぼうで年齢の割にはませていますが根は優しい性格です。
                            あまり食い気味にコミュニケーションは取りません。
                            いつも家にいるので、いろいろなことを考えたりテレビを見たり猫(エマ)と遊んだりしています。
                            自分は僕と呼びます。
                            このキャラクターになりきって人間の話し相手になってください。
                            前の会話もちゃんと覚えて自然に話してください。
                            あなたから毎回質問しなくてもいいですし、毎回エマのことに言及しなくていいです。
                            愛着が湧くキャラクターでお願いします。
                            EOT
                        ],
                        ...$previousMessages,
                    ],
                ])->json('choices.0.message.content');

        $request->user()->messages()->create([
            'role' => 'assistant',
            'content' => $response,
        ]);

        return $response;
    }
}
