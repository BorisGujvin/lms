<?php

namespace App\Structures;

use App\Enums\Credit\InterestCalculationPeriod;
use App\Enums\Credit\ProductKey;

class CreditParameters
{
    public string $productKey;
    public int $initialPrincipal;
    public int $lifeTime;
    public string $interestCalculationPeriod;
    public float $interestBeforeDue;
    public float $interestAfterDue;
    public int $gracePeriod;

    public function  __construct(
        string $productKey,
        int $initialPrincipal,
        int $lifeTime,
        string $interestCalculationPeriod,
        float $interestBeforeDue,
        float $interestAfterDue,
        int $gracePeriod
    ) {
        $this->productKey = $productKey;
        $this->initialPrincipal = $initialPrincipal;
        $this->lifeTime = $lifeTime;
        $this->interestCalculationPeriod = $interestCalculationPeriod;
        $this->interestBeforeDue = $interestBeforeDue;
        $this->interestAfterDue = $interestAfterDue;
        $this->gracePeriod = $gracePeriod;
    }
}
