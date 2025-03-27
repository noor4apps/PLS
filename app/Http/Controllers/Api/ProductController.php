<?php

namespace App\Http\Controllers\Api;

use App\Actions\GetApplicablePriceAction;
use App\Actions\GetProductsAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(ProductRequest $request, GetProductsAction $getProductsAction): ProductCollection
    {
        $products = $getProductsAction->execute($request);

        return new ProductCollection($products);
    }

    public function show(Product $product, ProductRequest $request, GetApplicablePriceAction $getApplicablePriceAction): ProductResource
    {
        $product->load('priceLists');
        $product->price = $getApplicablePriceAction->execute($product, $request->query());

        return new ProductResource($product);
    }
}
