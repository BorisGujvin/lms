<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCreditRequest;
use App\Http\Requests\PaymentReceivedRequest;
use App\Models\Credit;
use App\Services\CreditService;

class CreditController extends Controller
{
    private CreditService $creditService;

    public function __construct(CreditService $creditService)
    {
        $this->creditService = $creditService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateCreditRequest $request)
    {
        $result = $this->creditService->createCredit(
            $request->get('credit_ref'),
            $request->get('borrower_name'),
            $request->get('product_key'),
            $request->get('currency'),
            $request->get('life_time'),
            $request->get('initial_principal'),
            $request->get('interest_calculation_period'),
            $request->get('interest_before_due'),
            $request->get('interest_after_due'),
            $request->get('grace_period'),
            $request->get('credited_at') ?? now()->format('Y-m-d H:i:s'),
        );

        return Response($result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function show(string $id)
    {
        $credit = Credit::with('transactions', 'transactions.transactionItems')->find($id);
        $balance = $this->creditService->getCreditBalance($id);

        return ['credit' => $credit, 'balance' => $balance];
    }

    public function calculateInterest(string $id)
    {
        return $this->creditService->accrueInterest($id);
    }
    public function paymentReceived(PaymentReceivedRequest $request, string $id)
    {
        return $this->creditService->paymentReceived($id, $request->get('amount'));
    }
}
