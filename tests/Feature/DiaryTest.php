<?php

use App\Models\Diary;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;

it('can show diaries index page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('diaries.index'));

    $response->assertOk();
});

it('can show multiple diaries', function () {
    // 2025-01-01 is Wednesday
    // Calendar should show from 2024-12-30(Monday) to 2025-02-02(Sunday)

    $this->travelTo(Carbon::createFromFormat('Y-m', '2025-01'), function () {
        $user = User::factory()->create();

        $diaryDates = [
            '2024-12-30', '2025-01-01', '2025-01-03', '2025-01-31', '2025-02-01',
        ];
        $diaries = Diary::factory(5)
            ->for($user)
            ->sequence(
                fn (Sequence $sequence) => ['date' => $diaryDates[$sequence->index]],
            )
            ->create();

        $response = $this->actingAs($user)->get(route('diaries.index'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Diary/Index')
            ->has('calendarDates', 35) // 5weeks
            ->has('calendarDates.0', fn (Assert $page) => $page
                ->where('date', '2024-12-30')
                ->where('diaryId', $diaries[0]->id)
                ->etc()
            )
            ->has('calendarDates.1', fn (Assert $page) => $page
                ->where('date', '2024-12-31')
                ->where('diaryId', null)
                ->etc()
            )
            ->has('calendarDates.2', fn (Assert $page) => $page
                ->where('date', '2025-01-01')
                ->where('diaryId', $diaries[1]->id)
                ->etc()
            )
            ->has('calendarDates.4', fn (Assert $page) => $page
                ->where('date', '2025-01-03')
                ->where('diaryId', $diaries[2]->id)
                ->etc()
            )
            ->has('calendarDates.32', fn (Assert $page) => $page
                ->where('date', '2025-01-31')
                ->where('diaryId', $diaries[3]->id)
                ->etc()
            )
            ->has('calendarDates.33', fn (Assert $page) => $page
                ->where('date', '2025-02-01')
                ->where('diaryId', $diaries[4]->id)
                ->etc()
            )
            ->has('calendarDates.34', fn (Assert $page) => $page
                ->where('date', '2025-02-02')
                ->where('diaryId', null)
                ->etc()
            )
        );
    });
});

it('can show diary create page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('diaries.create', ['date' => '2025-01-01']));

    $response->assertOk();
});

it('cannot show the existing diary create page', function () {
    $user = User::factory()->create();
    Diary::factory()->for($user)->create(['date' => '2025-01-01']);

    $response = $this->actingAs($user)->get(route('diaries.create', ['date' => '2025-01-01']));

    $response->assertRedirect(route('diaries.index'));
});

it('can store a diary', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('diaries.store', '2025-01-01'), [
        'content' => 'Content',
    ]);

    $response->assertRedirect(route('diaries.show', ['diary' => '2025-01-01']));
    $this->assertDatabaseHas('diaries', [
        'user_id' => $user->id,
        'date' => '2025-01-01',
        'content' => 'Content',
    ]);
});

it('can show a diary', function () {
    $user = User::factory()->create();
    $diary = Diary::factory()->for($user)->create(['date' => '2025-01-01']);

    $response = $this->actingAs($user)->get(route('diaries.show', ['diary' => $diary->date->format('Y-m-d')]));

    $response->assertOk();
});

it('can update a diary', function () {
    $user = User::factory()->create();
    $diary = Diary::factory()->for($user)->create(['date' => '2025-01-01']);

    $response = $this->actingAs($user)->put(route('diaries.update', ['diary' => $diary->date->format('Y-m-d')]), [
        'content' => 'Updated Content',
    ]);

    $response->assertRedirect(route('diaries.show', ['diary' => $diary->date->format('Y-m-d')]));
    $this->assertDatabaseHas('diaries', [
        'user_id' => $user->id,
        'date' => '2025-01-01',
        'content' => 'Updated Content',
    ]);
});

it('can delete a diary', function () {
    $user = User::factory()->create();
    $diary = Diary::factory()->for($user)->create(['date' => '2025-01-01']);

    $response = $this->actingAs($user)->delete(route('diaries.destroy', ['diary' => $diary->date->format('Y-m-d')]));

    $response->assertRedirect(route('diaries.index'));
    $this->assertDatabaseMissing('diaries', [
        'user_id' => $user->id,
        'date' => '2025-01-01',
    ]);
});
