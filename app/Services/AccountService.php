<?php

namespace App\Services;

use App\Models\Account;
use App\Models\TransactionItem;

class AccountService
{
    public function foundOrCreateByKey(string $accountKey, string $currency)
    {
        $account = Account::where('account_key', $accountKey)->first();
        if (!$account) {
            $account = Account::create([
                'account_key' => $accountKey,
                'currency' => $currency
            ]);
        }

        return $account;
    }

    /**
     * @param string $accountKey
     * @return int|mixed
     * @throws \Exception
     *
     */
    public function getAccountBalance(string $accountKey)
    {
        $account = Account::with('items')->where('account_key', $accountKey)->first();
        if (!$account) {
            return 0;
        }
        return $account->items->sum('amount_subunit');
    }
}
