<?php

namespace App\Models;

use App\Enums\MutationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mutation extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'freezer_id',
        'product_id',
        'alteration',
        'type',
        'stock_before',
        'stock_after'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'type' => MutationType::class
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });

        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc');
        });
    }

    /**
     * The shop where the mutation took place.
     *
     * @return BelongsTo
     */
    public function shop(): BelongsTo
    {
        return $this->freezer->shop();
    }

    /**
     * The freezer where the mutation took place.
     *
     * @return BelongsTo
     */
    public function freezer(): BelongsTo
    {
        return $this->belongsTo(Freezer::class);
    }

    /**
     * The product that triggered the mutation.
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Search mutations by a certain product.
     *
     * @param  Builder  $query
     * @param  Product  $product
     * @return Builder
     */
    public function scopeWhereProduct(Builder $query, Product $product): Builder
    {
        return $query->where('product_id', $product->id);
    }
}
