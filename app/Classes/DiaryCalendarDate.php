<?php

namespace App\Classes;

class DiaryCalendarDate
{
    public function __construct(
        public int $id,
        public ?int $diaryId,
        public string $date,
        public bool $isToday,
        public bool $isCurrentMonth,
    ) {}
}
