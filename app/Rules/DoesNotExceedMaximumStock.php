<?php

namespace App\Rules;

use App\Models\Freezer;
use App\Models\Product;
use Illuminate\Contracts\Validation\Rule;

class DoesNotExceedMaximumStock implements Rule
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
     * The maximum stock of the freezer, either newly provided or already existing.
     *
     * @var int
     */
    protected $maxStock;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Freezer $freezer, Product|int $product, ?int $maxStock = null)
    {
        $this->freezer = $freezer;
        $this->product = ($product instanceof Product) ? $product : Product::find($product);

        $freezerProductLink = $this->freezer->product($this->product);

        $this->currentStock = $freezerProductLink?->current_stock;
        $this->maxStock = $maxStock ?? $freezerProductLink->max_stock;
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
        if ($attribute === 'current_stock' && $value > $this->maxStock) {
            return false;
        }

        if ($attribute === 'alteration' && ($this->currentStock + $value) > $this->maxStock) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The maximum stock ({$this->maxStock}) may not be exceeded.";
    }
}
