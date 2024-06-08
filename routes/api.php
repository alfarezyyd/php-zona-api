<?php

  use App\Http\Controllers\CategoryController;
  use App\Http\Controllers\ProductController;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Route;

  Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
  });

  Route::prefix('products')->group(function () {
    Route::get('', [ProductController::class, 'index']);
    Route::post('', [ProductController::class, 'store']);
    Route::post('/edit/{productSlug}', [ProductController::class, 'update']);
    Route::delete('/{productSlug}', [ProductController::class, 'destroy']);
  });

  Route::prefix('categories')->group(function () {
    Route::get('', [CategoryController::class, 'index']);
    Route::post('', [CategoryController::class, 'store']);
    Route::post('/edit/{categoryId}', [CategoryController::class, 'update']);
    Route::delete('/{categoryId}', [CategoryController::class, 'destroy']);
  });
