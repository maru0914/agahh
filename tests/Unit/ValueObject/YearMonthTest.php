<?php

use App\ValueObjects\YearMonth;

it('throw exception when invalid format', function () {
    YearMonth::fromString('2021-01');
})->throws(InvalidArgumentException::class, 'Invalid YearMonth format. Expected YYYYMM.');
