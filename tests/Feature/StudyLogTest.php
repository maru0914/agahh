<?php

use App\Models\StudyLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('allows a user to create a study log', function () {
    $user = User::factory()->create();
    actingAs($user);

    $response = post(route('study-logs.store'), [
        'studied_at' => now()->toDateString(),
        'subject' => '数学 問題集',
        'started_time' => '10:00',
        'ended_time' => '11:30',
        'notes' => '章末問題',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('study_logs', [
        'user_id' => $user->id,
        'subject' => '数学 問題集',
        'duration_minutes' => 90, // computed
    ]);
});

it('lists monthly logs with total minutes', function () {
    $user = User::factory()->create();
    actingAs($user);

    StudyLog::factory()->create([
        'user_id' => $user->id,
        'studied_at' => now()->startOfMonth()->toDateString(),
        'duration_minutes' => 60,
    ]);
    StudyLog::factory()->create([
        'user_id' => $user->id,
        'studied_at' => now()->startOfMonth()->addDay()->toDateString(),
        'duration_minutes' => 30,
    ]);

    $response = get(route('study-logs.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('StudyLog/Index')
        ->where('totalMinutes', 90)
    );
});

it('allows update and delete only by owner', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $log = StudyLog::factory()->create([
        'user_id' => $owner->id,
        'duration_minutes' => 45,
    ]);

    // owner can update
    actingAs($owner);
    $resp = patch(route('study-logs.update', $log), [
        'studied_at' => $log->studied_at->toDateString(),
        'subject' => '更新',
        'started_time' => '10:00',
        'ended_time' => '10:50',
        'notes' => null,
    ]);
    $resp->assertRedirect();
    expect($log->refresh()->duration_minutes)->toBe(50);

    // other cannot view/update/delete
    actingAs($other);
    get(route('study-logs.show', $log))->assertNotFound();
    patch(route('study-logs.update', $log), [
        'studied_at' => $log->studied_at->toDateString(),
        'subject' => 'x',
        'started_time' => '00:00',
        'ended_time' => '00:10',
    ])->assertNotFound();
    delete(route('study-logs.destroy', $log))->assertNotFound();
});
