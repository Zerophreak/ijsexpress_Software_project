<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Freezer extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'model_name'
    ];

    /**
     * Return the shop the freezer is associated with.
     *
     * @return BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * The products associated with this freezer, including the stock information.
     *
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('current_stock', 'max_stock')
            ->using(FreezerProductConnection::class);
    }

    /**
     * Retrieve the pivot connection of this freezer with a specific product.
     *
     * @param  Product  $product
     * @return FreezerProductConnection|null
     */
    public function product(Product $product): ?FreezerProductConnection
    {
        if (!$this->products->contains($product)) {
            return null;
        }

        return $this->products->find($product->id)->pivot;
    }

    /**
     * Retrieve all the mutations in this freezer.
     *
     * @return HasMany
     */
    public function mutations(): HasMany
    {
        return $this->hasMany(Mutation::class);
    }
}
