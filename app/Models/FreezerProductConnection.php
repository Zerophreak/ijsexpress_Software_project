<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

class FreezerProductConnection extends Pivot
{
    /**
     * Return all the mutations for this product in this freezer.
     *
     * @return Builder
     */
    public function mutations(): Builder
    {
        return Mutation::query()
            ->where('product_id', $this->product_id)
            ->where('freezer_id', $this->freezer_id);
    }

    /**
     * Add a new mutation for the related freezer and product.
     *
     * @param  array  $mutation
     * @return Mutation
     */
    public function createMutation(array $mutation): Mutation
    {
        return Mutation::create([
            'freezer_id'   => $this->freezer_id,
            'product_id'   => $this->product_id,
            'alteration'   => $mutation['alteration'],
            'type'         => $mutation['type'],
            'stock_before' => $this->current_stock,
            'stock_after'  => $mutation['stock_after']
        ]);
    }
}
