<?php

namespace App\Actions;

use App\Classes\DiaryCalenderDate;
use App\ValueObjects\YearMonth;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class GenerateDiaryCalenderDates
{
    protected static int $dateId = 1;

    /** @var Collection<DiaryCalenderDate> */
    protected Collection $dates;

    /**
     * Execute the action.
     *
     * @return Collection<DiaryCalenderDate>
     */
    public function handle(?YearMonth $yearMonth = null): Collection
    {
        $yearMonth ??= YearMonth::current();
        $target = $yearMonth->toCarbonImmutable();
        $this->dates = collect();
        $days = $target->daysInMonth;

        for ($i = 1; $i <= $days; $i++) {
            if ($i === 1) {
                $this->addPreviousMonthDays($target);
            }
            $this->dates->push(
                new DiaryCalenderDate(
                    id: self::$dateId++,
                    diaryId: null,
                    date: $i,
                    isToday: $target->isToday(),
                    isCurrentMonth: true,
                    isChecked: false,
                ));
            if ($i === $days) {
                $this->addNextMonthDays($target);
            }
            $target = $target->addDay();
        }

        return $this->dates;
    }

    private function addPreviousMonthDays(CarbonImmutable $target): void
    {
        $firstDayOfMonth = $target->startOfMonth();
        $dayOfWeekOfFirstDay = $firstDayOfMonth->dayOfWeek;

        $isSunday = $dayOfWeekOfFirstDay === CarbonInterface::SUNDAY;
        $additionalDays = $isSunday ? 6 : $dayOfWeekOfFirstDay - CarbonInterface::MONDAY;

        for ($j = $additionalDays; $j > 0; $j--) {
            $date = $firstDayOfMonth->subDays($j);
            $this->dates->push(
                new DiaryCalenderDate(
                    id: self::$dateId++,
                    diaryId: null,
                    date: $date->day,
                    isToday: false,
                    isCurrentMonth: false,
                    isChecked: false,
                ));
        }
    }

    private function addNextMonthDays(CarbonImmutable $target): void
    {
        $lastDayOfMonth = $target->endOfMonth();
        $dayOfWeekOfLastDay = $lastDayOfMonth->dayOfWeek;

        $isSunday = $dayOfWeekOfLastDay === CarbonInterface::SUNDAY;
        $additionalDays = $isSunday ? 0 : 7 - $dayOfWeekOfLastDay;

        for ($j = 1; $j <= $additionalDays; $j++) {
            $date = $lastDayOfMonth->addDays($j);
            $this->dates->push(
                new DiaryCalenderDate(
                    id: self::$dateId++,
                    diaryId: null,
                    date: $date->day,
                    isToday: false,
                    isCurrentMonth: false,
                    isChecked: false,
                ));
        }
    }
}
