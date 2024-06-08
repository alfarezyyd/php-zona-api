<?php

  namespace App\Http\Resources;

  use App\Models\Category;
  use Illuminate\Http\Request;
  use Illuminate\Http\Resources\Json\JsonResource;

  class CategoryResource extends JsonResource
  {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
      return [
        "id" => $this['id'],
        "slug" => $this['slug'],
        "name" => $this['name'],
        'parent_category' => $this->getParentCategory(), // Mengambil parent category
      ];
    }

    // Fungsi untuk mendapatkan parent category
    private function getParentCategory(): ?CategoryResource
    {
      // Jika tidak ada category_id, return null
      if (!$this['category_id']) {
        return null;
      }

      // Lakukan query untuk mendapatkan parent category berdasarkan category_id
      $parentCategory = Category::find($this['category_id']);

      return $parentCategory ? new CategoryResource($parentCategory) : null;
    }
  }
