<?php

namespace App\Rules;

use App\Models\Freezer;
use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;

class IsNotLessThanCurrentStock implements Rule
{
    /**
     * The freezer that is being validated.
     *
     * @var Freezer
     */
    protected $freezer;

    /**
     * The product that is being validated.
     *
     * @var Product
     */
    protected $product;

    /**
     * The current stock of the product in the freezer.
     *
     * @var int
     */
    protected $currentStock;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Freezer $freezer, Product $product, ?int $currentStock = null, ?int $alteration = null)
    {
        $this->freezer = $freezer;
        $this->product = $product;

        $freezerProductLink = $this->freezer->product($product);

        $this->currentStock = $alteration
            ? $freezerProductLink->current_stock + $alteration
            : $currentStock ?? $freezerProductLink->current_stock;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $value >= $this->currentStock;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The maximum stock may not be lower than the current stock.';
    }
}
