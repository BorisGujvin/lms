<?php

namespace App\Enums\Credit;

use App\Enums\BaseEnum;

class Currency extends BaseEnum
{
    public const EUR = 'EUR';
    public const SEK = 'SEK';
    public const UAH = 'UAH';

    public static function getLabels(): array
    {
        return [
            self::EUR => self::EUR,
            self::SEK => self::SEK,
            self::UAH => self::UAH,
        ];
    }
}
