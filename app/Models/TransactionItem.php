<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsTo;
  use Illuminate\Database\Eloquent\Relations\Pivot;

  class TransactionItem extends Pivot
  {
    use HasFactory;

    protected $table = 'transaction_items';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
      'transaction_id',
      'product_id',
      'quantity',
      'subtotal',
    ];

    public function product(): BelongsTo
    {
      return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function transaction(): BelongsTo
    {
      return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }
  }
