<?php

    namespace App\Http\Controllers;

    use App\Helpers\CommonHelper;
    use App\Http\Requests\SaveProductRequest;
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
            //
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
                    $this->productResourceService->store($validatedSaveProductRequest->file('images'), $productModel->id);
                }
                $this->productCategoryService->store($validatedSaveProductRequest['category_ids'], $productModel);
                DB::commit();
                return response()
                    ->json((new WebResponsePayload("Product created successfully"))
                        ->getJsonResource())->setStatusCode(201);
            } catch (Throwable) {
                DB::rollBack();
                throw new HttpResponseException(response()->json(
                    (new WebResponsePayload("Internal server error",))
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
        public function update(SaveProductRequest $saveProductRequest, Product $productModel): JsonResponse
        {
            $validatedProductSaveRequest = $saveProductRequest->validated();
            $productModel->fill($validatedProductSaveRequest);
            $saveState = $productModel->save();
            $this->commonHelper->validateOperationState($saveState);
            return response()
                ->json((new WebResponsePayload("Product updated successfully"))
                    ->getJsonResource());
        }

        /**
         * Remove the specified resource from storage.
         */
        public function destroy(string $productSlug): JsonResponse
        {
            Product::query()->where('slug', $productSlug)->delete();
            return response()
                ->json((new WebResponsePayload("Product deleted successfully"))
                    ->getJsonResource());
        }
    }
