<?php

namespace App\Models;

use App\Enums\ShopType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Shop extends Model
{
    use SoftDeletes, HasRelationships;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'customer_number',
        'name',
        'type',
        'street',
        'house_number',
        'postal_code',
        'city',
        'logo_url'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'type' => ShopType::class
    ];

    /**
     * The freezers associated with this shop.
     *
     * @return HasMany
     */
    public function freezers(): HasMany
    {
        return $this->hasMany(Freezer::class);
    }

    /**
     * Return all the produts associated with the shop.
     *
     * @return HasManyDeep
     */
    public function products(): HasManyDeep
    {
        return $this->hasManyDeep(Product::class, [Freezer::class, 'freezer_product'])
            ->withPivot('freezer_product', ['current_stock', 'max_stock']);
    }

    /**
     * Return all the produts associated with the shop with the
     * current and max stocks summed together.
     *
     * @return Collection
     */
    protected function getSummedProductsAttribute(): Collection
    {
        return $this->products->groupBy('id')->transform(function ($products) {
            $currentStockSum = $products->sum('freezer_product.current_stock');
            $maxStockSum = $products->sum('freezer_product.max_stock');

            $product = $products->first();

            data_set($product, 'freezer_product.current_stock', $currentStockSum);
            data_set($product, 'freezer_product.max_stock', $maxStockSum);

            return $product;
        });
    }

    /**
     * Return the total stock indication of this shop.
     *
     * @return int|null
     */
    protected function getStockLevelAttribute(): ?int
    {
        $summedProducts = $this->summed_products;

        if ($summedProducts->isEmpty()) {
            return null;
        }

        $totalCurrentStock = $summedProducts->sum('freezer_product.current_stock');
        $totalMaxStock = $summedProducts->sum('freezer_product.max_stock');

        return (int)round(($totalCurrentStock / $totalMaxStock) * 100);
    }

    /**
     * Return all the mutations that took place in the shop.
     *
     * @return HasManyDeep
     */
    public function mutations(): HasManyDeep
    {
        return $this->hasManyDeep(Mutation::class, [Freezer::class]);
    }
}
