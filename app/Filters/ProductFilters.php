<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProductFilters
{
    public static function apply(Collection $products, Request $request): Collection
    {
        if ($request->has('order')) {
            $order = $request->get('order');

            if ($order === 'lowest-to-highest') {
                return $products->sortBy('price');
            } elseif ($order === 'highest-to-lowest') {
                return $products->sortByDesc('price');
            }
        }

        return $products;
    }
}
