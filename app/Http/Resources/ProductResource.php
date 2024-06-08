<?php

  namespace App\Http\Resources;

  use App\Models\Category;
  use App\Models\Store;
  use Illuminate\Http\Request;
  use Illuminate\Http\Resources\Json\JsonResource;

  class ProductResource extends JsonResource
  {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
      return [
        'id' => optional($this->resource)['id'],
        'slug' => optional($this->resource)['slug'],
        'name' => optional($this->resource)['name'],
        'description' => optional($this->resource)['description'],
        'price' => optional($this->resource)['price'],
        'stock' => optional($this->resource)['stock'],
        'sku' => optional($this->resource)['sku'],
        'produced_by' => optional($this->resource)['produced_by'],
        'status' => optional($this->resource)['status'],
        'resources' => ProductResourceResource::collection($this->whenLoaded('resources')),
        'category' => new CategoryResource($this->whenLoaded('category'))
      ];
    }
  }
