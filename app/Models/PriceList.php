<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceList extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'country_code', 'country_id', 'currency_code', 'currency_id', 'price', 'starts_at', 'ends_at', 'priority'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
