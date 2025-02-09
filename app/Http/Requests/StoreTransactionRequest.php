<?php

  namespace App\Http\Requests;

  use Illuminate\Foundation\Http\FormRequest;

  class StoreTransactionRequest extends FormRequest
  {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
      return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
      return [
        'transaction_date' => 'required|date',
        'description' => 'required|string',
        'order_payload.*.id' => ["required", "numeric", "exists:products,id"],
        'order_payload.*.quantity' => ["required", "numeric", "min:1"],
        'order_payload.*.price' => ["required", "numeric", "min:1"],
        'total_price' => ["required", "numeric", "min:1"],
        'tax' => ["required", "numeric", "min:1"],
      ];
    }
  }
