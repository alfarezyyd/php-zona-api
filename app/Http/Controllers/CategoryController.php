<?php

  namespace App\Http\Controllers;

  use App\Helpers\CommonHelper;
  use App\Http\Requests\StoreCategoryRequest;
  use App\Http\Requests\UpdateCategoryRequest;
  use App\Http\Resources\CategoryResource;
  use App\Models\Category;
  use App\Payloads\WebResponsePayload;
  use Illuminate\Database\QueryException;
  use Illuminate\Http\JsonResponse;
  use Illuminate\Support\Str;

  class CategoryController extends Controller
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
      $categoriesModel = Category::query()->get();
      return response()
        ->json((new WebResponsePayload("Category retrieved successfully", jsonResource: CategoryResource::collection($categoriesModel)))
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
     * @throws \HttpResponseException
     */
    public function store(StoreCategoryRequest $storeCategoryRequest): JsonResponse
    {
      $validatedCategorySaveRequest = $storeCategoryRequest->validated();
      $validatedCategorySaveRequest['slug'] = Str::slug($validatedCategorySaveRequest['name']);
      $categoryModel = new Category($validatedCategorySaveRequest);
      $saveState = $categoryModel->save($validatedCategorySaveRequest);
      $this->commonHelper->validateOperationState($saveState);
      return response()
        ->json((new WebResponsePayload("Category created successfully"))
          ->getJsonResource())->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
      //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
      //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $updateCategoryRequest, int $categoryId): JsonResponse
    {
      $validatedCategorySaveRequest = $updateCategoryRequest->validated();
      $categoryModel = Category::query()->findOrFail($categoryId);
      $categoryModel->fill($validatedCategorySaveRequest);
      $categoryModel->save();
      return response()
        ->json((new WebResponsePayload("Category updated successfully"))
          ->getJsonResource());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $categoryId): JsonResponse
    {
      try {
        Category::query()->findOrFail($categoryId)->delete();
        return response()
          ->json((new WebResponsePayload("Category deleted successfully"))
            ->getJsonResource());
      } catch (QueryException) {
        return response()
          ->json((new WebResponsePayload("Category can't be deleted"))
            ->getJsonResource())->setStatusCode(400);
      }
    }
  }
