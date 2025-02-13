<?php

namespace App\ValueObjects;

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;
use InvalidArgumentException;

final class YearMonth
{
    private string $value;

    private function __construct(string $yearMonth)
    {
        $this->value = $yearMonth;

        if (! $this->isValidYearMonth()) {
            throw new InvalidArgumentException('Invalid YearMonth format. Expected YYYYMM.');
        }
    }

    /*********************************************************
     * Factories
     *******************************************************/

    public static function fromString(string $yearMonth): self
    {
        return new self($yearMonth);
    }

    public static function fromCarbon(Carbon $carbon): self
    {
        return new self($carbon->format('Ym'));
    }

    public static function current(): self
    {
        return self::fromCarbon(now());
    }

    /*********************************************************
     * Getters
     *******************************************************/

    public function getYear(): string
    {
        return substr($this->value, 0, 4);
    }

    public function getMonth(): string
    {
        return substr($this->value, 4, 2);
    }

    /*********************************************************
     * Converters
     *******************************************************/

    public function __toString(): string
    {
        return $this->value;
    }

    public function toCarbon(): Carbon
    {
        return Carbon::create($this->getYear(), $this->getMonth());
    }

    public function toCarbonImmutable(): CarbonImmutable
    {
        return CarbonImmutable::create($this->getYear(), $this->getMonth());
    }

    public function previous(): self
    {
        return self::fromCarbon($this->toCarbon()->subMonth());
    }

    public function next(): self
    {
        return self::fromCarbon($this->toCarbon()->addMonth());
    }

    /*********************************************************
     * Comparators
     *******************************************************/

    public function equals(YearMonth $other): bool
    {
        return $this->value === (string) $other;
    }

    public function isValidYearMonth(): bool
    {
        return preg_match('/^\d{4}(0[1-9]|1[0-2])$/', $this->value) === 1;
    }

    public function isCurrentYearMonth(): bool
    {
        return $this->equals(self::current());
    }
}
