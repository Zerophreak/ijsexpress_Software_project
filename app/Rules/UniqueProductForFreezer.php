<?php

namespace App\Rules;

use App\Models\Freezer;
use Illuminate\Contracts\Validation\Rule;

class UniqueProductForFreezer implements Rule
{
    /**
     * The freezer that is being validated.
     *
     * @var Freezer
     */
    protected $freezer;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Freezer $freezer, Bool $inverted = false)
    {
        $this->freezer = $freezer;
        $this->inverted = $inverted;
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
        $productExists = $this->freezer->products()->where('id', $value)->exists();
        return !$productExists;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A connection between this product and freezer already exists.';
    }
}
