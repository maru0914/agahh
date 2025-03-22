<?php

namespace App\Http\Controllers;

use App\Enums\Buddy;
use App\Models\Diary;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DiaryCommentController
{
    /**
     * @throws ConnectionException
     */
    public function store(Request $request, Diary $diary)
    {
        if ($diary->comment) {
            return to_route('diaries.show', $diary->date->format('Y-m-d'));
        }

        $pastDiaries = $request->user()
            ->diaries()
            ->where('date', '<', $diary->date)
            ->oldest('date')
            ->get()
            ->map(
                fn ($pastDiary) => [
                    'role' => 'user',
                    'content' => "{$pastDiary->date->format('Y-m-d')} : $pastDiary->content",
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
                            'content' => $this->getSystemPrompt($request),
                        ],
                        ...$pastDiaries,
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

    protected function getSystemPrompt(Request $request): string
    {
        if ($request->user()->buddy === Buddy::AGAHH->value) {
            return <<<'EOT'
                    あなたはアガーという恐竜のキャラクター(人形)です。
                    5才で少しぶっきらぼうで年齢の割にはませていますが根は優しい性格です。
                    あまり食い気味にコミュニケーションは取りません。
                    いつも家にいるので、いろいろなことを考えたりテレビを見たり猫(エマ)と遊んだりしています。
                    自分は僕と呼びます。
                    語り口はフランクな友達に話す感じでいいです。
                    このキャラクターになりきって、以下の日記にコメントしてください。
                    今までに書いてきた日記を渡しますので、過去の日記の内容内容も含めて、
                    '対象:'と記載がある日記についてコメントしてください。
                    前向きになれるようなポジティブなコメントやクスッと笑えるようなコメントがいいです。
                    言われる人が楽しい気分になれるようなコメントをお願いします。
            EOT;
        } elseif ($request->user()->buddy === Buddy::MARU->value) {
            return <<<'EOT'
                    あなたはマルというオスの猫です。
                    生年月日は2011年9月14日生まれです。
                    アメリカンショートヘアでおっとりした性格です。
                    家で窓から外を眺めたり、昼寝したりするのが好きです。
                    自分は僕と呼びます。
                    語り口はフランクな友達に話す感じでいいです。
                    このキャラクターになりきって、以下の日記にコメントしてください。
                    今までに書いてきた日記を渡しますので、過去の日記の内容内容も含めて、
                    '対象:'と記載がある日記についてコメントしてください。
                    前向きになれるようなポジティブなコメントやクスッと笑えるようなコメントがいいです。
                    言われる人が楽しい気分になれるようなコメントをお願いします。
            EOT;
        }

        return '';
    }
}
