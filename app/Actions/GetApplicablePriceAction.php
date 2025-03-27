<?php

namespace App\Actions;

use App\Exceptions\PriceListAmbiguityException;
use App\Models\Product;
use Carbon\Carbon;

class GetApplicablePriceAction
{
    public function execute(Product $product, array $params)
    {

        // Parse date parameter or use current date
        $date = isset($params['date'])
            ? Carbon::parse($params['date'])
            : Carbon::now();

        // Get country and currency filters from the parameters
        $countryCode = $params['country_code'] ?? null;
        $currencyCode = $params['currency_code'] ?? null;

        // Retrieve price lists that are valid for the current date range
        $query = $product->priceLists()
            ->whereDate('starts_at', '<=', $date)
            ->whereDate('ends_at', '>=', $date);

        // Filter by country:
        // - If provided, accept matching country_code or general (NULL)
        // - If not provided, consider only general entries (NULL)
        $query->where(function ($q) use ($countryCode) {
            if ($countryCode) {
                $q->where('country_code', $countryCode)
                    ->orWhereNull('country_code');
            } else {
                $q->whereNull('country_code');
            }
        });

        // Filter by currency
        $query->where(function ($q) use ($currencyCode) {
            if ($currencyCode) {
                $q->where('currency_code', $currencyCode)
                    ->orWhereNull('currency_code');
            } else {
                $q->whereNull('currency_code');
            }
        });

        // Order the results based on match specificity:
        // 1. Exact match: both country_code and currency_code are not NULL.
        // 2. Partial match: one is not NULL.
        // 3. General: both are NULL.
        // Then sort by priority (lowest first).
        $query->orderByRaw("
            CASE
                WHEN country_code IS NOT NULL AND currency_code IS NOT NULL THEN 1
                WHEN country_code IS NOT NULL AND currency_code IS NULL THEN 2
                WHEN country_code IS NULL AND currency_code IS NOT NULL THEN 3
                ELSE 4
            END, priority ASC
        ");

        // Get all matching price lists
        $priceLists = $query->get();

        // If no valid price list found, return product's base price
        if ($priceLists->isEmpty()) {
            return $product->base_price;
        }

        // Select the first price list from the sorted results
        $selectedPriceList = $priceLists->first();

        // Check for duplicates: if more than one price list has identical country, currency, and priority,
        // then we have ambiguous pricing and should throw an exception.
        $duplicates = $priceLists->filter(function ($item) use ($selectedPriceList) {
            return $item->country_code === $selectedPriceList->country_code
                && $item->currency_code === $selectedPriceList->currency_code
                && $item->priority === $selectedPriceList->priority;
        });

        if ($duplicates->count() > 1) {
            throw new PriceListAmbiguityException();
        }

        return $selectedPriceList->price;
    }
}
