<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with(['transactionItems'])->get();

        return Response(TransactionResource::collection($transactions));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return ('No form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTransactionRequest $request)
    {
        $items = collect($request->get('items'));
        $summ = $items->sum('amount_subunit');
        if ($summ) {
            return Response('non-zero amount', Response::HTTP_BAD_REQUEST);
        }
        $transactionService = new TransactionService();
        $transaction = $transactionService->commitTransaction(
            $request->get('transaction_key'),
            $items
        );

        return Response(TransactionResource::make($transaction));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transactions = Transaction::with(['transactionItems'])->findOrFail($id);

        return Response(TransactionResource::make($transactions));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return ('no updates');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return ('revercr');
    }
}
