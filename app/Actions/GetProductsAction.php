<?php

namespace App\Actions;

use App\Filters\ProductFilters;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class GetProductsAction
{
    public function __construct(private GetApplicablePriceAction $getApplicablePriceAction) {}

    public function execute(Request $request): Collection
    {
        $query = Product::with('priceLists')->get();

        $products = $query->map(function ($product) use ($request) {
            $product->price = $this->getApplicablePriceAction->execute($product, $request->query());
            return $product;
        });

        $filteredProducts = ProductFilters::apply(collect($products), $request);

        return $filteredProducts->map(fn ($product) => new ProductResource($product));
    }
}
