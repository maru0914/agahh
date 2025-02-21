<?php

namespace Database\Seeders;

use App\Models\Diary;
use App\Models\DiaryComment;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Random\RandomException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /****************************************
         * Users
         ****************************************/
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        /****************************************
         * Diaries
         ****************************************/
        $diaryDates = collect(range(1, 100))
            ->map(fn ($daysAgo) => now()->subDays($daysAgo)->format('Y-m-d'))
            ->shuffle()
            ->take(30);

        $diaryDates->each(function ($date) use ($user) {
            Diary::factory()
                ->for($user)
                ->create(['date' => $date]);
        });

        /****************************************
         * Diary Comments
         ****************************************/

        $diaries = Diary::all();

        $diaries->each(function ($diary) {
            try {
                if (random_int(0, 1)) {
                    DiaryComment::factory()
                        ->for($diary)
                        ->create();
                }
            } catch (RandomException $e) {
                Log::info('RandomException: '.$e->getMessage());
            }
        });

    }
}
