# PHPStan level 6 の残り30件（2026-10-01 21:2x に baseline を外して実行）

実行: `vendor/bin/phpstan analyse`（level 6・larastan）。baseline に凍結している30件を、baseline なしで出した一覧。

| #  | 場所                                                       | 種類                          | 内容                                                                                                                                           |
|----|----------------------------------------------------------|-----------------------------|----------------------------------------------------------------------------------------------------------------------------------------------|
| 1  | `app/Actions/GenerateDiaryCalendarDates.php:18`          | `missingType.generics`      | Property App\Actions\GenerateDiaryCalendarDates::$diaryCalendarDates with generic class Illuminate\Support\Collection does not specify its t |
| 2  | `app/Actions/GenerateDiaryCalendarDates.php:29`          | `missingType.generics`      | Method App\Actions\GenerateDiaryCalendarDates::handle() return type with generic class Illuminate\Support\Collection does not specify its ty |
| 3  | `app/Http/Controllers/Api/ChatController.php:16`         | `missingType.return`        | Method App\Http\Controllers\Api\ChatController::__invoke() has no return type specified.                                                     |
| 4  | `app/Http/Controllers/Auth/VerifyEmailController.php:22` | `argument.type`             | Parameter #1 $user of class Illuminate\Auth\Events\Verified constructor expects Illuminate\Contracts\Auth\MustVerifyEmail, App\Models\User\| |
| 5  | `app/Http/Controllers/DiaryCommentController.php:16`     | `missingType.return`        | Method App\Http\Controllers\DiaryCommentController::store() has no return type specified.                                                    |
| 6  | `app/Http/Controllers/DiaryController.php:24`            | `missingType.return`        | Method App\Http\Controllers\DiaryController::index() has no return type specified.                                                           |
| 7  | `app/Http/Controllers/DiaryController.php:45`            | `missingType.return`        | Method App\Http\Controllers\DiaryController::create() has no return type specified.                                                          |
| 8  | `app/Http/Controllers/DiaryController.php:63`            | `missingType.return`        | Method App\Http\Controllers\DiaryController::store() has no return type specified.                                                           |
| 9  | `app/Http/Controllers/DiaryController.php:79`            | `missingType.return`        | Method App\Http\Controllers\DiaryController::show() has no return type specified.                                                            |
| 10 | `app/Http/Controllers/DiaryController.php:99`            | `missingType.return`        | Method App\Http\Controllers\DiaryController::update() has no return type specified.                                                          |
| 11 | `app/Http/Controllers/DiaryController.php:111`           | `missingType.return`        | Method App\Http\Controllers\DiaryController::destroy() has no return type specified.                                                         |
| 12 | `app/Http/Controllers/StudyLogController.php:18`         | `missingType.return`        | Method App\Http\Controllers\StudyLogController::index() has no return type specified.                                                        |
| 13 | `app/Http/Controllers/StudyLogController.php:51`         | `missingType.return`        | Method App\Http\Controllers\StudyLogController::create() has no return type specified.                                                       |
| 14 | `app/Http/Controllers/StudyLogController.php:79`         | `missingType.return`        | Method App\Http\Controllers\StudyLogController::store() has no return type specified.                                                        |
| 15 | `app/Http/Controllers/StudyLogController.php:106`        | `missingType.return`        | Method App\Http\Controllers\StudyLogController::show() has no return type specified.                                                         |
| 16 | `app/Http/Controllers/StudyLogController.php:134`        | `missingType.return`        | Method App\Http\Controllers\StudyLogController::update() has no return type specified.                                                       |
| 17 | `app/Http/Controllers/StudyLogController.php:158`        | `missingType.return`        | Method App\Http\Controllers\StudyLogController::destroy() has no return type specified.                                                      |
| 18 | `app/Http/Controllers/WelcomeController.php:10`          | `missingType.return`        | Method App\Http\Controllers\WelcomeController::__invoke() has no return type specified.                                                      |
| 19 | `app/Http/Requests/Diary/IndexRequest.php:10`            | `missingType.iterableValue` | Method App\Http\Requests\Diary\IndexRequest::rules() return type has no value type specified in iterable type array.                         |
| 20 | `app/Http/Requests/Diary/StoreRequest.php:15`            | `missingType.iterableValue` | Method App\Http\Requests\Diary\StoreRequest::rules() return type has no value type specified in iterable type array.                         |
| 21 | `app/Http/Requests/Diary/UpdateRequest.php:15`           | `missingType.iterableValue` | Method App\Http\Requests\Diary\UpdateRequest::rules() return type has no value type specified in iterable type array.                        |
| 22 | `app/Http/Requests/StudyLog/StoreRequest.php:12`         | `missingType.iterableValue` | Method App\Http\Requests\StudyLog\StoreRequest::rules() return type has no value type specified in iterable type array.                      |
| 23 | `app/Http/Requests/StudyLog/UpdateRequest.php:12`        | `missingType.iterableValue` | Method App\Http\Requests\StudyLog\UpdateRequest::rules() return type has no value type specified in iterable type array.                     |
| 24 | `app/Http/Resources/DiaryResource.php:14`                | `missingType.iterableValue` | Method App\Http\Resources\DiaryResource::toArray() return type has no value type specified in iterable type array.                           |
| 25 | `app/Models/Diary.php:46`                                | `missingType.generics`      | Method App\Models\Diary::scopeSameMonth() has parameter $query with generic class Illuminate\Database\Eloquent\Builder but does not specify  |
| 26 | `app/Models/Message.php:12`                              | `missingType.generics`      | Class App\Models\Message uses generic trait Illuminate\Database\Eloquent\Factories\HasFactory but does not specify its types: TFactory       |
| 27 | `app/Models/Message.php:24`                              | `missingType.generics`      | Method App\Models\Message::scopeToday() has parameter $query with generic class Illuminate\Database\Eloquent\Builder but does not specify it |
| 28 | `app/Models/StudyLog.php:17`                             | `missingType.generics`      | Class App\Models\StudyLog uses generic trait Illuminate\Database\Eloquent\Factories\HasFactory but does not specify its types: TFactory      |
| 29 | `database/factories/MessageFactory.php:8`                | `missingType.generics`      | Class Database\Factories\MessageFactory extends generic class Illuminate\Database\Eloquent\Factories\Factory but does not specify its types: |
| 30 | `database/factories/StudyLogFactory.php:8`               | `missingType.generics`      | Class Database\Factories\StudyLogFactory extends generic class Illuminate\Database\Eloquent\Factories\Factory but does not specify its types |

## 種類ごとの数

- `missingType.return` 15（コントローラの戻り値の型）
- `missingType.generics` 8（コレクション・クエリ・ファクトリの型引数）
- `missingType.iterableValue` 6（rules()・toArray() の配列の中身の型）
- `argument.type` 1（#4。理由つきで凍結済み）

## 10/2 07:55 にやること

16件を選び、AI のコード補助ありの8件・なしの8件に分ける（分けるのは 10/3 09:00 でもよい）。

## 選んだ16件（2026-10-01 21:41 決定）: AI あり8件・AI なし8件

よく似た2件を組にし、片方を AI あり、もう片方を AI なしで直す。組の差が「AI を使ったかどうか」だけになるようにする。

| 組 | 種類      | AI あり                          | AI なし                             | 先にやる方 |
|---|---------|--------------------------------|-----------------------------------|-------|
| 1 | 戻り値の型   | #6 DiaryController::index      | #12 StudyLogController::index     | AI あり |
| 2 | 戻り値の型   | #7 DiaryController::create     | #13 StudyLogController::create    | AI なし |
| 3 | 戻り値の型   | #8 DiaryController::store      | #14 StudyLogController::store     | AI あり |
| 4 | 戻り値の型   | #9 DiaryController::show       | #15 StudyLogController::show      | AI なし |
| 5 | 配列の中身の型 | #20 Diary\StoreRequest::rules  | #22 StudyLog\StoreRequest::rules  | AI あり |
| 6 | 配列の中身の型 | #21 Diary\UpdateRequest::rules | #23 StudyLog\UpdateRequest::rules | AI なし |
| 7 | 型の引数    | #26 Message（HasFactory）        | #28 StudyLog（HasFactory）          | AI あり |
| 8 | 型の引数    | #29 MessageFactory             | #30 StudyLogFactory               | AI なし |

- 「先にやる方」を組ごとに入れ替えるのは、先に直した方の答えを見て、後の方が速くなる分を両側に散らすため。
- 残り14件（#1〜5・#10・#11・#16〜19・#24・#25・#27）は、この16件のあとに回す。#4 は理由つきで凍結済み。

## 記録の表（1件ごとに書く）

| 組 | #  | AI | 始め   | 終わり  | 分 | phpstan 0件 | テスト（落ちた回数） | メモ |
|---|----|----|------|------|---|------------|------------|----|
| 1 | 6  | あり | 8:39 | 8:40 | 1 | o          | 環境なし・未実行   |    |
| 1 | 12 | なし | 8:44 | 8:45 | 1 | o          | 環境なし・未実行   |    |
