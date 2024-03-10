<?php

namespace App\Services;

use App\Enums\Credit\Currency;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    /**
     * @param string $transactionKey
     *
     * @param Collection $items
     * @param null $created_at
     * @param null $creditId
     * @return mixed
     * @throws \Exception
     */
    public function commitTransaction (
        string $transactionKey,
        Collection $items,
        $created_at = null,
        $creditId = null
    ) {
        if (!$created_at) {
            $created_at = now()->format('Y-m-d H:i:s');
        }
        if (!$this->validate($items)) {
            throw new \Exception('Invalid transaction items');
        }
        return $this->_commitTransaction($transactionKey, $items, $created_at, $creditId);
    }

    private function _commitTransaction(
        string $transactionKey,
        Collection $items,
        $created_at,
        $creditId
    ) {
        DB::beginTransaction();
            $transaction = Transaction::create([
                'transaction_key' => $transactionKey,
                'credit_id' => $creditId,
                'created_at' => $created_at,
                'updated_at' => $created_at,
            ]);
            foreach ($items as $item) {

                $transactionItem = TransactionItem::create(array_merge($item, [
                    'transaction_id' => $transaction->id,
                    'created_at' => $created_at,
                    'updated_at' => $created_at,
                ]));
            }
        DB::commit();

        return $transaction;
    }

    public function validate(Collection $items)
    {
        $grouped = $items->groupBy('currency');
        foreach ($grouped as $currency => $items) {
            $sum = $items->sum('amount_subunit');
            if ($sum or !Currency::getValue($currency)) {
                return false;
            }
        }

        return true;
    }
}
