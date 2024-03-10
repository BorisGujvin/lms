<?php

namespace App\Enums\Credit;

use App\Enums\BaseEnum;

class ProductKey extends BaseEnum
{
    public const CREDIT_120_DAYS = 'credit_120_days';
    public const LEASING_120_DAYS = 'leasing_120_days';

    public static function getLabels(): array
    {
        return [
            self::CREDIT_120_DAYS => self::CREDIT_120_DAYS,
            self::LEASING_120_DAYS => self::LEASING_120_DAYS,
        ];
    }
}
