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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'diaryId' => $this->diaryId,
            'date' => $this->date,
            'isToday' => $this->isToday,
            'isCurrentMonth' => $this->isCurrentMonth,
        ];
    }
}
