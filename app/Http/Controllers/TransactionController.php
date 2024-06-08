<?php

  namespace App\Http\Controllers;

  use App\Helpers\CommonHelper;
  use App\Http\Requests\StoreTransactionRequest;
  use App\Http\Requests\UpdateTransactionRequest;
  use App\Http\Resources\TransactionResource;
  use App\Models\Transaction;
  use App\Models\TransactionItem;
  use App\Payloads\WebResponsePayload;
  use Illuminate\Http\Exceptions\HttpResponseException;
  use Illuminate\Support\Facades\DB;

  class TransactionController extends Controller
  {
    private CommonHelper $commonHelper;

    /**
     * @param CommonHelper $commonHelper
     */
    public function __construct(CommonHelper $commonHelper)
    {
      $this->commonHelper = $commonHelper;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $transactionModel = Transaction::query()->with('products')->get();
      return response()
        ->json((new WebResponsePayload("Transaction retrieve successfully", jsonResource: TransactionResource::collection($transactionModel)))
          ->getJsonResource())->setStatusCode(200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $storeTransactionRequest)
    {
      $validatedStoreTransactionRequest = $storeTransactionRequest->validated();
      $transactionModel = new Transaction($validatedStoreTransactionRequest);
      try {
        DB::beginTransaction();
        $transactionModel->save();
        $transactionItems = [];
        foreach ($validatedStoreTransactionRequest['order_payload'] as $transaction) {
          $transactionItem['transaction_id'] = $transactionModel['id'];
          $transactionItem['product_id'] = $transaction['id'];
          $transactionItem['quantity'] = $transaction['quantity'];
          $transactionItem['subtotal'] = $transaction['price'] * $transaction['quantity'];
          $transactionItems[] = $transactionItem;
        }
        TransactionItem::query()->insert($transactionItems);
        DB::commit();
        return response()
          ->json((new WebResponsePayload("Transaction created successfully"))
            ->getJsonResource())->setStatusCode(201);
      } catch (\Exception $exception) {
        DB::rollBack();
        throw new HttpResponseException(response()->json(
          (new WebResponsePayload($exception->getMessage()))
            ->getJsonResource())->setStatusCode(500)
        );
      }
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
      //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
      //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTransactionRequest $storeTransactionRequest, int $transactionId)
    {
      $transactionModel = Transaction::query()->findOrFail($transactionId)->first();
      $validatedUpdateTransactionRequest = $storeTransactionRequest->validated();
      try {
        DB::beginTransaction();
        $transactionModel->fill($validatedUpdateTransactionRequest);
        $updateState = $transactionModel->save();
        $this->commonHelper->validateOperationState($updateState);
        $transactionModel->products()->detach();
        $transactionItems = [];
        foreach ($validatedUpdateTransactionRequest['order_payload'] as $transaction) {
          $transactionItem['transaction_id'] = $transactionModel['id'];
          $transactionItem['product_id'] = $transaction['id'];
          $transactionItem['quantity'] = $transaction['quantity'];
          $transactionItem['subtotal'] = $transaction['price'] * $transaction['quantity'];
          $transactionItems[] = $transactionItem;
        }
        TransactionItem::query()->insert($transactionItems);
        DB::commit();
        return response()
          ->json((new WebResponsePayload("Product updated successfully"))
            ->getJsonResource());
      } catch (\Exception $exception) {
        DB::rollBack();
        throw new HttpResponseException(response()->json(
          (new WebResponsePayload($exception->getMessage()))
            ->getJsonResource())->setStatusCode(500)
        );
      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $transactionId)
    {
      $transactionModel = Transaction::query()->with('products')->where('id', $transactionId)->first();
      $transactionModel->products()->detach();
      $transactionModel->delete();
      return response()
        ->json((new WebResponsePayload("Transaction deleted successfully"))
          ->getJsonResource());
    }
  }
