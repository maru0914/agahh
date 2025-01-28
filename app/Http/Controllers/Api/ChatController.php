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
        return Http::withToken(config('services.openai.key'))
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
                            EOT
                        ],
                        [
                            'role' => 'user',
                            'content' => $request->input('content'),
                        ],
                    ],
                ])->json('choices.0.message.content');
    }
}
