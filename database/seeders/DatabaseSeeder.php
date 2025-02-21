<?php

namespace Database\Seeders;

use App\Models\Diary;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
    }
}
