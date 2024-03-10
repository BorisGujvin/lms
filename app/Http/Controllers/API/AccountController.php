<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAccountRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use http\Env\Response;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = Account::all();

        return Response(AccountResource::collection($accounts));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return ('no forms');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAccountRequest $request)
    {

        $account = Account::create($request->only('account_key', 'currency'));

        return Response(AccountResource::make($account));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
