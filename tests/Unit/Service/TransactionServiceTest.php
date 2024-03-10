<?php

namespace Tests\Unit\Service;

use App\Services\TransactionService;
use PHPUnit\Framework\TestCase;

class TransactionServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_validation(): void
    {
        $transactionService =new TransactionService();
        $result = $transactionService->validate(collect([
            [
                'account_id' => null,
                'amount_subunit' => 10,
                'currency' => 'EUR'
            ],
            [
                'account_id' => null,
                'amount_subunit' => -10,
                'currency' => 'EUR'
            ],
        ]));
        $this->assertEquals($result, true);

        $result = $transactionService->validate(collect([
            [
                'account_id' => null,
                'amount_subunit' => 11,
                'currency' => 'EUR'
            ],
            [
                'account_id' => null,
                'amount_subunit' => -10,
                'currency' => 'EUR'
            ],
        ]));
        $this->assertEquals($result, false);

        $result = $transactionService->validate(collect([
            [
                'account_id' => null,
                'amount_subunit' => 10,
                'currency' => 'EUR'
            ],
            [
                'account_id' => null,
                'amount_subunit' => -10,
                'currency' => 'SEK'
            ],
        ]));
        $this->assertEquals($result, false);

        $result = $transactionService->validate(collect([
            [
                'account_id' => null,
                'amount_subunit' => 10,
                'currency' => 'EUK'
            ],
            [
                'account_id' => null,
                'amount_subunit' => -10,
                'currency' => 'EUK'
            ],
        ]));
        $this->assertEquals($result, false);

    }
}
