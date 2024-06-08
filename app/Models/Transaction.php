<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsToMany;

  class Transaction extends Model
  {
    use HasFactory;

    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
      'transaction_date',
      'total_price',
      'tax',
      'description'
    ];

    public function products(): BelongsToMany
    {
      return $this->belongsToMany(Product::class, 'transaction_items', 'transaction_id', 'product_id')
        ->withPivot(['quantity', 'subtotal'])
        ->using(TransactionItem::class);
    }
  }
