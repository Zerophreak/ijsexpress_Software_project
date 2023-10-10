<?php

namespace App\Http\Requests\Product;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name'     => ['required', 'string', 'max:150'],
            'ean'      => [
                'required',
                'integer',
                Rule::unique('products')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ],
            'chip_id'  => ['required', 'integer'],
            'logo_url' => ['nullable', 'url'],
        ];
    }
}
