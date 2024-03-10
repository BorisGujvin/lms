<?php

namespace App\Console\Commands;

use App\Enums\Credit\CreditStatus;
use App\Enums\Credit\InterestCalculationPeriod;
use App\Models\Credit;
use App\Services\CreditService;
use Illuminate\Console\Command;

class UpdateCredits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credits:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private CreditService $creditService;

    /**
     * Execute the console command.
     */
    public function handle(CreditService $creditService)
    {
        $this->creditService = $creditService;
        $this->setOverdueStatuses();
        $this->accrueInterestMonthly();
        $this->accrueInterestDaily();
    }

    public function setOverdueStatuses()
    {
        Credit
            ::where('status', CreditStatus::STATUS_PERFORMING)
            ->where('due_at', '<', now())
            ->update(['status' => CreditStatus::STATUS_OVERDUE]);
    }

    public function accrueInterestMonthly()
    {
        $credits = Credit
            ::where('product_parameters->interestCalculationPeriod', InterestCalculationPeriod::MONTHLY)
            ->whereIn('status', [CreditStatus::STATUS_PERFORMING, CreditStatus::STATUS_OVERDUE])
            ->whereDay('credited_at', today()->day)
            ->get();
        foreach ($credits as $credit) {
            $this->creditService->accrueInterest($credit->id);
        }
    }

    public function accrueInterestDaily()
    {
        $credits = Credit
            ::where('product_parameters->interestCalculationPeriod', InterestCalculationPeriod::DAILY)
            ->whereIn('status', [CreditStatus::STATUS_PERFORMING, CreditStatus::STATUS_OVERDUE])
            ->get();
        foreach ($credits as $credit) {
            $this->creditService->accrueInterest($credit->id);
        }
    }

}
