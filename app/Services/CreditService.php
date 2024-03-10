<?php

namespace App\Services;

use App\Enums\Credit\CreditStatus;
use App\Enums\Credit\InterestCalculationPeriod;
use App\Models\Credit;
use App\Structures\CreditParameters;

class CreditService
{
    public function __construct(
        TransactionService $transactionService,
        AccountService $accountService
    ) {
        $this->transactionService = $transactionService;
        $this->accountService = $accountService;
    }
    public function createCredit(
        string $creditRef,
        string $borrowerName,
        string $productKey,
        string $currency,
        int $lifeTime,
        int $initialPrincipal,
        string $interestCalculationPeriod,
        float $interestBeforeDue,
        float $interestAfterDue,
        int $gracePeriod,
        string $createdAt
    ) {
        $creditParameters=new CreditParameters(
            $productKey,
            $initialPrincipal,
            $lifeTime,
            $interestCalculationPeriod,
            $interestBeforeDue,
            $interestAfterDue,
            $gracePeriod
        );
        $credit = Credit::create([
            'credit_ref' => $creditRef,
            'borrower_name' => $borrowerName,
            'product_key' => $productKey,
            'product_parameters' => $creditParameters,
            'currency' => $currency,
            'status' => CreditStatus::STATUS_INIT,
            'due_at' => today()->addDays($lifeTime),
            'initial_principal' => $initialPrincipal,
            'remaining_principal' => $initialPrincipal,
        ]);
        $transactionKey = $this->getNewCreditTransactionKey($credit);
        $items = $this->prepareNewCreditTransaction($credit);
        $this->transactionService->commitTransaction($transactionKey, $items, $createdAt, $credit->id);
        $credit->update([
            'credited_at' => $createdAt,
            'status' => CreditStatus::STATUS_PERFORMING,
        ]);

        return $credit;
    }

    public function getCreditBalance(string $id)
    {
        $credit = Credit::find($id);
        if (!$credit) {
            return Response(null, 404);
        }
        $bodyAccountKey = $this->getBorrowerBodyAccountKey($credit);
        $interestAccountKey = $this->getBorrowerAccountInterestKey($credit);
        return [
          'body' => $this->accountService->getAccountBalance($bodyAccountKey),
          'interest' => $this->accountService->getAccountBalance($interestAccountKey)
        ];
    }

    public function accrueInterest(string $id)
    {
        $credit = Credit::find($id);
        if (!$credit) {
            return Response(null, 404);
        }
        $creditParameters = $credit->product_parameters;
        $start_calculation = $credit->credited_at->addDays($creditParameters['gracePeriod']);
        $today = today();
        if ($today < $start_calculation) {
            return Response("crace period till {$start_calculation}");
        }
        $annualInterest = $credit->due_at > $today
            ? $creditParameters['interestBeforeDue']
            : $creditParameters['interestAfterDue'];
        switch ($creditParameters['interestCalculationPeriod']) {
            case InterestCalculationPeriod::MONTHLY:
                $interest = $annualInterest / 12;
                break;
            case InterestCalculationPeriod::DAILY:
                $interest = $annualInterest / 365;
                break;
        }

        $accountKey = $this->getBorrowerBodyAccountKey($credit);
        $remainingAmount = $this->accountService->getAccountBalance($accountKey);
        $interestAmount = round($remainingAmount * $interest);
        $transactionKey = $this->getCalculateInterestTransactionKey($credit);
        $items = $this->prepareInterestAccuralTransaction($credit, $interestAmount);
        $this->transactionService->commitTransaction($transactionKey, $items, null, $credit->id);
        return $interestAmount;
    }

    public function paymentReceived(string $id, $amount)
    {
        $credit = Credit::find($id);
        if (!$credit) {
            return Response(null, 404);
        }
        $interestAccountKey = $this->getBorrowerAccountInterestKey($credit);
        $interestAmount = $this->accountService->getAccountBalance($interestAccountKey);
        $interestPaymentAmount = min($interestAmount, $amount);
        $bodyPaymentAmount = $amount - $interestPaymentAmount;
        $transactionKey = $this->getPaymentTransactionKey($credit);
        $items = $this->preparePaymentReceiving($credit, $interestPaymentAmount, $bodyPaymentAmount);
        $this->transactionService->commitTransaction($transactionKey, $items, null, $credit->id);
        $status = $this->getCreditBalance($credit->id);
        $toUpdate = ['remaining_principal' => $status['body']];
        if ($status['body'] + $status['interest'] <= 0) {
           $toUpdate['status'] = CreditStatus::STATUS_SETTLED;
           $toUpdate['settled_at'] = now();
        }
        $credit->update($toUpdate);
    }

    private function prepareNewCreditTransaction(Credit $credit) {
        $borrowerAccountKey = $this->getBorrowerBodyAccountKey($credit);
        $creditAccountKey = $this->getCreditAccountKey($credit->currency);
        $borrowerAccount = $this->accountService->foundOrCreateByKey($borrowerAccountKey, $credit->currency);
        $ourAccount = $this->accountService->foundOrCreateByKey($creditAccountKey, $credit->currency);
        return collect([
            [
                'account_id' => $borrowerAccount->id,
                'amount_subunit' => $credit->initial_principal,
                'currency' => $credit->currency
            ],
            [
                'account_id' => $ourAccount->id,
                'amount_subunit' => (-1) * $credit->initial_principal,
                'currency' => $credit->currency
            ]

        ]);
    }

    private function prepareInterestAccuralTransaction(Credit $credit, int $amount) {
        $borrowerAccountKey = $this->getBorrowerAccountInterestKey($credit);
        $creditAccountKey = $this->getCreditAccountInterestKey($credit->currency);
        $borrowerAccount = $this->accountService->foundOrCreateByKey($borrowerAccountKey, $credit->currency);
        $ourAccount = $this->accountService->foundOrCreateByKey($creditAccountKey, $credit->currency);
        return collect([
            [
                'account_id' => $borrowerAccount->id,
                'amount_subunit' => $amount,
                'currency' => $credit->currency
            ],
            [
                'account_id' => $ourAccount->id,
                'amount_subunit' => (-1) * $amount,
                'currency' => $credit->currency
            ]

        ]);
    }

    private function preparePaymentReceiving(Credit $credit, int $interestAmount, $bodyAmount) {
        $borrowerAccountInterestKey = $this->getBorrowerAccountInterestKey($credit);
        $borrowerAccountBodyKey = $this->getBorrowerBodyAccountKey($credit);
        $creditAccountInterestKey = $this->getCreditAccountInterestKey($credit->currency);
        $creditAccountBodyKey = $this->getCreditAccountKey($credit->currency);
        $borrowerInterestAccount = $this->accountService->foundOrCreateByKey($borrowerAccountInterestKey, $credit->currency);
        $borrowerBodyAccount = $this->accountService->foundOrCreateByKey($borrowerAccountBodyKey, $credit->currency);
        $ourInterestAccount = $this->accountService->foundOrCreateByKey($creditAccountInterestKey, $credit->currency);
        $ourBodyAccount = $this->accountService->foundOrCreateByKey($creditAccountBodyKey, $credit->currency);
        $items = [];
        if ($interestAmount) {
            $items = [
                [
                    'account_id' => $borrowerInterestAccount->id,
                    'amount_subunit' => (-1) * $interestAmount,
                    'currency' => $credit->currency
                ],
                [
                    'account_id' => $ourInterestAccount->id,
                    'amount_subunit' => $interestAmount,
                    'currency' => $credit->currency
                ]
            ];
        }
        if ($bodyAmount) {
            $items = array_merge($items,[
                [
                    'account_id' => $borrowerBodyAccount->id,
                    'amount_subunit' => (-1) * $bodyAmount,
                    'currency' => $credit->currency
                ],
                [
                    'account_id' => $ourBodyAccount->id,
                    'amount_subunit' => $bodyAmount,
                    'currency' => $credit->currency
                ]
            ]);
        }
        return collect($items);
    }

    private function getBorrowerBodyAccountKey(Credit $credit) {
        return $credit->id . ':' . $credit->currency . ':body';
    }

    private function getBorrowerAccountInterestKey(Credit $credit) {
        return $credit->id . ':' . $credit->currency . ':interest';
    }

    private function getCreditAccountKey($currency) {
        return "credits:account({$currency})";
    }

    private function getCreditAccountInterestKey($currency) {
        return "interests:account({$currency})";
    }

    private function getNewCreditTransactionKey(Credit $credit) {
        return 'new credit:' . $credit->id;
    }

    private function getCalculateInterestTransactionKey(Credit $credit) {
        return 'interest:' . $credit->id . ':' . now()->format('Y-m-d H:i:s');
    }

    private function getPaymentTransactionKey(Credit $credit) {
        return 'payment:' . $credit->id . ':' . now()->format('Y-m-d H:i:s');
    }
}
