<?php

namespace App\Http\Requests\FreezerProductLink;

use App\Models\Product;
use Illuminate\Validation\Rule;
use App\Rules\UniqueProductForFreezer;
use App\Rules\DoesNotExceedMaximumStock;
use Illuminate\Foundation\Http\FormRequest;

class StoreFreezerProductConnectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'product_id'    => ['required', 'exists:products,id', new UniqueProductForFreezer($this->freezer)],

            'current_stock' => [
                'sometimes',
                'numeric',
                'gt:0',
                'lt:200',
                Rule::when(function () {
                    return Product::where('id', $this->product_id)->exists();
                }, function () {
                    return new DoesNotExceedMaximumStock($this->freezer, $this->product_id, $this->max_stock);
                })
            ],

            'max_stock'     => ['required', 'numeric', 'gt:0', 'lt:200']
        ];
    }
}
