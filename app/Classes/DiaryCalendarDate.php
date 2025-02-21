<?php

namespace App\Classes;

class DiaryCalendarDate
{
    public function __construct(
        public int $id,
        public ?int $diaryId,
        public int $date,
        public bool $isToday,
        public bool $isCurrentMonth,
    ) {}
}
