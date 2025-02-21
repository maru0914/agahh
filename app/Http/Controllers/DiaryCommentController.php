<?php

namespace App\Http\Controllers;

use App\Models\Diary;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class DiaryCommentController
{
    /**
     * @throws ConnectionException
     */
    public function store(Diary $diary)
    {
        if ($diary->comment) {
            return to_route('diaries.show', $diary->date->format('Y-m-d'));
        }

        $previousDiaries = Diary::where('date', '<', $diary->date)
            ->oldest('date')
            ->get()
            ->map(
                fn ($diary) => [
                    'role' => 'user',
                    'content' => $diary->content,
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
                            このキャラクターになりきって、以下の日記にコメントしてください。
                            複数の日付の日記を渡しますので、他の日付の日記の内容も含めて、'対象:'と記載がある日記にコメントしてください。
                            前向きになれるようなポジティブなコメントやクスッと笑えるようなコメントがいいです。
                            言われる人が楽しい気分になれるようなコメントをお願いします。
                            EOT
                        ],
                        ...$previousDiaries,
                        [
                            'role' => 'user',
                            'content' => '対象:¥n'.$diary->date->format('Y-m-d').': ¥n'.$diary->content,
                        ],
                    ],
                ])->json('choices.0.message.content');

        $diary->comment()->create([
            'content' => $response,
        ]);

        return to_route('diaries.show', $diary->date->format('Y-m-d'));
    }
}
