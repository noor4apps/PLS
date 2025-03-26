<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['name', 'base_price', 'description'];

    public function priceLists(): HasMany
    {
        return $this->hasMany(PriceList::class);
    }
}
