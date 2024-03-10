<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_key' => $this->transaction_key,
            'created_at' => $this->created_at,
            'items' => TransactionItemResource::collection($this->transactionItems)
        ];
    }
}
