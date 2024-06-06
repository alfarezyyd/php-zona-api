<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsToMany;
    use Illuminate\Database\Eloquent\Relations\HasMany;

    class Product extends Model
    {
        use HasFactory;

        protected $table = 'products';
        protected $primaryKey = 'id';
        protected $keyType = 'int';
        public $incrementing = true;
        public $timestamps = true;
        protected $fillable = [
            'slug',
            'name',
            'description',
            'price',
            'stock',
            'sku',
            'produced_by',
            'status',
            'image_path'
        ];

        public function categories(): BelongsToMany
        {
            return $this->belongsToMany(Category::class, "product_categories", "product_id", "category_id");
        }

        public function resources(): HasMany
        {
            return $this->hasMany(ProductResource::class, 'product_id', 'id');
        }

    }
