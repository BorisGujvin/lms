<?php

namespace App\Enums\Credit;

use App\Enums\BaseEnum;

class CreditStatus extends BaseEnum
{
    public const STATUS_INIT = 'init';
    public const STATUS_PERFORMING = 'performing';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_SETTLED = 'settled';
    public const STATUS_VOIDED = 'voided';

    public static function getLabels(): array
    {
        return [
            self::STATUS_INIT => self::STATUS_INIT,
            self::STATUS_PERFORMING => self::STATUS_PERFORMING,
            self::STATUS_OVERDUE => self::STATUS_OVERDUE,
            self::STATUS_SETTLED => self::STATUS_SETTLED,
            self::STATUS_VOIDED => self::STATUS_VOIDED,
        ];
    }
}
