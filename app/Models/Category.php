<?php

  namespace App\Models;

  use Illuminate\Database\Eloquent\Factories\HasFactory;
  use Illuminate\Database\Eloquent\Model;
  use Illuminate\Database\Eloquent\Relations\BelongsTo;
  use Illuminate\Database\Eloquent\Relations\HasMany;

  class Category extends Model
  {
    use HasFactory;

    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $with = ['parentCategory', 'childCategory'];
    protected $fillable = [
      'slug',
      'name',
      'parent_id' // Make sure this is correct
    ];

    public function childCategory(): HasMany
    {
      return $this->hasMany(Category::class, 'category_id');
    }

    public function parentCategory(): BelongsTo
    {
      return $this->belongsTo(Category::class);
    }

    public function Products(): HasMany
    {
      return $this->hasMany(Product::class, 'category_id');
    }
  }
