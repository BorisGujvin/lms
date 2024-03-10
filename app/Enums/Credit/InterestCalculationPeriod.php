<?php

namespace App\Enums\Credit;

use App\Enums\BaseEnum;

class InterestCalculationPeriod extends BaseEnum
{
    public const MONTHLY = 'monthly';
    public const DAILY = 'daily';

    public static function getLabels(): array
    {
        return [
            self::MONTHLY => self::MONTHLY,
            self::DAILY => self::DAILY,
        ];
    }
}
