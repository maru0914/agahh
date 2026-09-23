# Agahh
Agahh は、アガーというキャラクターとコミュニケーションをとりながら日々を記録していくアプリです！<br/>
日々を記録しながら、アガーに意見を聞いたり、たわいもない話ができます。<br/>
その他にもアガーと一緒に人生を豊かにできるような機能を拡充予定です！

## 主な機能
- [ ] アガーとテキストチャットできる機能
- [ ] 日記機能
  - [ ] 日記についてアガーがコメントしてくれる
- [ ] TODO機能
- [ ] リマインド機能 
- [ ] アガーと遊べる機能
- [ ] 話し相手を追加できる機能(ペットなど)

## 使用技術
- バックエンド: [Laravel](https://laravel.com/docs/11.x)
- フロント: [Vue](https://vuejs.org/)
- バックエンド/フロント結合: [Inertia.js](https://inertiajs.com/)
- CSSフレームワーク: [Tailwind CSS](https://tailwindcss.com/)
- UIフレームワーク: [shadcn-vue](https://www.shadcn-vue.com/)
- PHPテストフレームワーク: [Pest](https://pestphp.com/)
- DB: [SQLite](https://www.sqlite.org/)
- フロントビルド: [Vite](https://ja.vitejs.dev/)
- CI: [GitHub Actions](https://docs.github.com/ja/actions)
- デプロイ先: [Laravel Cloud](https://cloud.laravel.com)

## 環境構築
- [Laravel Herd](https://herd.laravel.com/)をインストール
- `~/Herd`で`git clone `
- 以下コマンドを実行
```bash
cd soccion
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate fresh --seed 
npm install
npm run dev 
```

http://agahh.test へアクセスして表示を確認





## 静的解析（PHPStan / Larastan）

2026-09-23 に Larastan 3.x を導入し、rule level を 5 から 8 まで1段ずつ上げて件数を実測した。
目的は「level をいくつにするか」を勘で決めず、各段で何が増えるかを見てから決めること。

```bash
composer require --dev "larastan/larastan:^3.0"
XDEBUG_MODE=off vendor/bin/phpstan analyse --memory-limit=1G
```

`phpstan.neon`:

```neon
includes:
    - vendor/larastan/larastan/extension.neon
parameters:
    paths: [app, database, routes]
    level: 5
```

### 実測（2026-09-23・解析対象 59 ファイル）

| level | 件数 | 増分 | 増えたものの性格 |
| ---: | ---: | ---: | --- |
| 5 | 22 | ― | 型情報の欠落が引き起こす誤検知と、実際の型不一致が混在 |
| 6 | 59 | +37 | 型注釈を書いていないことの指摘のみ。動作のバグではない |
| 7 | 59 | ±0 | 該当箇所なし |
| 8 | 105 | +46 | null 安全性。ここだけ実バグになりうる種類 |

### 各段の内訳

**level 5（22件）** — `property.notFound` 7／`method.nonObject`（`format()` on string）6／`nullCoalesce.offset` 4／`argument.type` 3／`argument.unresolvableType` 1／`method.notFound` 1。
`property.notFound` はいずれも `Illuminate\Database\Eloquent\Model::$date`／`$role`／`$content`。
モデル自身ではなく、リレーション経由で取り出した値が `Model` 型のままになっている箇所で出ている。

**level 6（+37件）** — `missingType.return` 15／`missingType.generics` 15／`missingType.iterableValue` 6／`method.unresolvableReturnType` 1。
全てが「型を書いていない」ことの指摘で、コードの振る舞いは変わらない。
`HasOne` / `BelongsTo` / `HasMany` / `Builder` / `Factory` / `Collection` に型引数が付いていないもの、
コントローラのメソッドに戻り値型が無いもの、`rules()` の `array` に値の型が無いものが大半。

**level 7（±0件）** — 「部分的に誤った union 型」を見る段。該当が1件も無かったため、
level 6 で baseline を作るなら 7 まで同時に上げられる。

**level 8（+46件）** — 大半が `auth()->user()` の戻り値が `App\Models\User|null` であることに由来する。
`->messages()`／`->buddy`／`->id`／`->fill()` などを null チェック無しで呼んでいる箇所が 30 件超。
ほかに `Illuminate\Support\Carbon|null` への `format()`／`diffInMinutes()` が 6 件、
`User|null` を非 null 引数へ渡している `argument.type` が 8 件、`YearMonth` の `return.type` が 2 件。

注意すべきは、**この 46 件の大半は実行時には落ちない**こと。
`auth` ミドルウェアを通るルートでは `user()` は必ず非 null だが、PHPStan はミドルウェアの保証を読めない。
したがって level 8 への対応は「コードを直す」より「型で保証を渡す」作業になる
（`$request->user()` を使う、モデルに `@property` を書く、Larastan の設定で解決する）。

### 運用方針

1. level 6 で `--generate-baseline` を作り、既存の指摘を凍結する。以後、新しく書くコードだけが level 6 を満たす。
2. baseline に入れたまま放置せず、種類ごとに少しずつ外して直す。件数ではなく「種類が1つ消えた」を単位にする。
3. level 8 は、null 安全性の型付けが済んでから上げる。件数だけを見て先に上げない。

### 次に確かめること（仮説）

level 6 の `missingType.generics`（リレーションの型引数）を埋めると、
level 5 の `property.notFound` 7 件も同時に消えるのではないか。
`Diary::comment(): HasOne` を `HasOne<DiaryComment, $this>` と書けば、
`$diary->comment->content` が `Model::$content` ではなく `DiaryComment::$content` として解決されるはず。
1 段上のルールを満たすことが 1 段下の件数を減らす、という関係が成り立つなら、
baseline を外していく順番はこの依存関係に沿って決めるのが速い。

### 環境についての記録

この Mac の既定 PHP は 8.5.5 だが、`composer.lock` は PHP 8.4 までしか解決できない
（`nette/schema` v1.3.2 が `php 8.1 - 8.4`、`brianium/paratest` v7.7.0 が `~8.2.0 || ~8.3.0 || ~8.4.0`）。
当面は Herd の `php84` を使う。8.5 へ上げるには `composer update` で依存を更新する必要があり、これは別の作業として扱う。
