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
        'transaction_date' => $this['transaction_date'],
        'total_price' => $this['total_price'],
        'tax' => $this['tax'],
        'description' => $this['description'],
        'products' => ProductResource::collection($this->whenLoaded('products'))
      ];
    }
  }
