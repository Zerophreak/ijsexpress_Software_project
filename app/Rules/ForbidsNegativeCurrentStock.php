<?php

namespace App\Rules;

use App\Models\Freezer;
use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;

class ForbidsNegativeCurrentStock implements Rule
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
    public function __construct(Freezer $freezer, Product $product)
    {
        $this->freezer = $freezer;
        $this->product = $product;

        $freezerProductLink = $this->freezer->product($product);

        $this->currentStock = $freezerProductLink->current_stock;
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
        return $this->currentStock + $value >= 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The current stock may not become negative.';
    }
}
