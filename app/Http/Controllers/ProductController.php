<?php

  namespace App\Http\Controllers;

  use App\Helpers\CommonHelper;
  use App\Http\Requests\SaveProductRequest;
  use App\Http\Resources\ProductResource;
  use App\Models\Product;
  use App\Payloads\WebResponsePayload;
  use App\Service\ProductCategoryService;
  use App\Service\ProductResourceService;
  use Illuminate\Http\Exceptions\HttpResponseException;
  use Illuminate\Http\JsonResponse;
  use Illuminate\Support\Facades\DB;
  use Illuminate\Support\Str;
  use Throwable;

  class ProductController extends Controller
  {
    private ProductResourceService $productResourceService;
    private ProductCategoryService $productCategoryService;
    private CommonHelper $commonHelper;

    /**
     * @param ProductResourceService $productResourceService
     * @param ProductCategoryService $productCategoryService
     * @param CommonHelper $commonHelper
     */
    public function __construct(ProductResourceService $productResourceService, ProductCategoryService $productCategoryService, \App\Helpers\CommonHelper $commonHelper)
    {
      $this->productResourceService = $productResourceService;
      $this->productCategoryService = $productCategoryService;
      $this->commonHelper = $commonHelper;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $productModel = Product::query()->with('resources')->with('category')->get();
      return response()
        ->json((new WebResponsePayload("Product retrieve successfully", jsonResource: ProductResource::collection($productModel)))
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
    public function store(SaveProductRequest $saveProductRequest): JsonResponse
    {
      $validatedSaveProductRequest = $saveProductRequest->validated();
      $validatedSaveProductRequest['slug'] = Str::slug($validatedSaveProductRequest['name']);
      $productModel = new Product($validatedSaveProductRequest);
      try {
        DB::beginTransaction();
        $productModel->save();

        if ($validatedSaveProductRequest['images'] !== null) {
          $this->productResourceService->store($saveProductRequest->file('images'), $productModel->id);
        }
        DB::commit();
        return response()
          ->json((new WebResponsePayload("Product created successfully"))
            ->getJsonResource())->setStatusCode(201);
      } catch (Throwable $exception) {
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
    public function show(Product $product)
    {
      //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
      //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveProductRequest $saveProductRequest, string $productSlug): JsonResponse
    {
      // Retrieve the product model by slug
      $productModel = Product::query()->where('slug', $productSlug)->firstOrFail();

      // Get the validated data from the request
      $validatedProductSaveRequest = $saveProductRequest->validated();

      // Check if there is an image file in the request
      if ($saveProductRequest->hasFile('image')) {
        // Handle the file upload
        $imagePath = $saveProductRequest->file('image')->store('images', 'public');

        // Add the image path to the validated data
        $validatedProductSaveRequest['image_path'] = $imagePath;
      }

      // Fill the product model with validated data
      $productModel->fill($validatedProductSaveRequest);

      // Save the updated product model
      $saveState = $productModel->save();

      // Validate the operation state
      $this->commonHelper->validateOperationState($saveState);

      // Return a successful response
      return response()
        ->json((new WebResponsePayload("Product updated successfully"))
          ->getJsonResource());
    }


    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $productSlug): JsonResponse
    {
      // Retrieve the product with its associated resources
      $product = Product::query()->with('resources')->where('slug', $productSlug)->first();

      if (!$product) {
        return response()->json(['message' => 'Product not found.'], 404);
      }

      // Delete the associated resources
      $product->resources()->delete();

      // Delete the product
      $product->delete();

      return response()
        ->json((new WebResponsePayload("Product deleted successfully"))
          ->getJsonResource());
    }
  }
