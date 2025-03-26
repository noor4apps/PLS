<?php

use App\Models\Country;
use App\Models\Currency;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->char('country_code', 3)->nullable()->index();
            $table->foreignIdFor(Country::class)->nullable()->constrained()->nullOnDelete();
            $table->char('currency_code', 3)->nullable()->index();
            $table->foreignIdFor(Currency::class)->nullable()->constrained()->nullOnDelete();
            $table->float('price');
            $table->dateTime('starts_at')->nullable()->index();
            $table->dateTime('ends_at')->nullable()->index();
            $table->unsignedInteger('priority')->default(0);
            $table->timestamps();

            $table->index(['country_code', 'currency_code', 'starts_at', 'ends_at'], 'price_list_lookup_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_lists');
    }
};
