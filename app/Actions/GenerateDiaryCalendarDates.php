<?php

namespace App\Actions;

use App\Classes\DiaryCalendarDate;
use App\Models\Diary;
use App\Models\User;
use App\ValueObjects\YearMonth;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class GenerateDiaryCalendarDates
{
    protected static int $dateId = 1;

    /** @var Collection<DiaryCalendarDate> */
    protected Collection $diaryCalendarDates;

    protected User $user;

    protected CarbonImmutable $yearMonth;

    /**
     * Execute the action.
     *
     * @return Collection<DiaryCalendarDate>
     */
    public function handle(User $user, ?YearMonth $yearMonth = null): Collection
    {
        $this->user = $user;
        $yearMonth ??= YearMonth::current();
        $this->yearMonth = $yearMonth->toCarbonImmutable();
        $this->diaryCalendarDates = collect();

        $this->addPreviousMonthDates();
        $this->addCurrentMonthDates();
        $this->addNextMonthDates();

        return $this->diaryCalendarDates;
    }

    /**
     * Add previous month days to the collection.
     */
    protected function addPreviousMonthDates(): void
    {
        $firstDateOfThisMonth = $this->yearMonth->startOfMonth();
        $previousMonthDatesCount = $this->countPreviousMonthDates($firstDateOfThisMonth);

        $this->addDiaryCalendarDates(
            startDate: $firstDateOfThisMonth->subDays($previousMonthDatesCount),
            days: $previousMonthDatesCount
        );
    }

    /**
     * Add current month days to the collection.
     */
    protected function addCurrentMonthDates(): void
    {
        $this->addDiaryCalendarDates(
            startDate: $this->yearMonth->startOfMonth(),
            days: $this->yearMonth->daysInMonth
        );
    }

    /**
     * Add next month days to the collection.
     */
    protected function addNextMonthDates(): void
    {
        $lastDayOfThisMonth = $this->yearMonth->endOfMonth()->startOfDay();
        $additionalDays = $this->countNextMonthDates($lastDayOfThisMonth);

        $this->addDiaryCalendarDates(
            startDate: $lastDayOfThisMonth->addDay(),
            days: $additionalDays
        );
    }

    /**
     * Add diary calendar dates to the collection.
     */
    protected function addDiaryCalendarDates(CarbonImmutable $startDate, int $days): void
    {
        $diaryDates = Diary::sameMonth($startDate)
            ->whereBelongsTo($this->user)
            ->select(['id', 'date'])
            ->get()
            ->keyBy('date');

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->addDays($i);
            $this->diaryCalendarDates->push(
                new DiaryCalendarDate(
                    id: self::$dateId++,
                    diaryId: $diaryDates->firstWhere('date', $date->format('Y-m-d H:i:s'))?->id,
                    date: $date->day,
                    isToday: $date->isToday(),
                    isCurrentMonth: $date->isSameMonth($this->yearMonth),
                ));
        }
    }

    /**
     * Count the previous month dates.
     */
    protected function countPreviousMonthDates(CarbonImmutable $firstDayOfMonth): int
    {
        $dayOfWeek = $firstDayOfMonth->dayOfWeek;

        return $dayOfWeek === CarbonInterface::SUNDAY ? 6 : $dayOfWeek - CarbonInterface::MONDAY;
    }

    /**
     * Count the next month dates.
     */
    protected function countNextMonthDates(CarbonImmutable $lastDayOfMonth): int
    {
        $dayOfWeek = $lastDayOfMonth->dayOfWeek;

        return $dayOfWeek === CarbonInterface::SUNDAY ? 0 : 7 - $dayOfWeek;
    }
}
