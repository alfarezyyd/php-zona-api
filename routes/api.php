<?php

  use App\Http\Controllers\CategoryController;
  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Route;

  Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
  });

  Route::prefix('products')->group(function () {
  });

  Route::prefix('categories')->group(function () {
    Route::get('', [CategoryController::class, 'index']);
    Route::post('', [CategoryController::class, 'store']);
  });
