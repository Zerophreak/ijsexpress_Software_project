<?php

namespace App\Http\Requests\Product;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\ForbidsEmptyRequests;

class UpdateProductRequest extends FormRequest
{
    use ForbidsEmptyRequests;

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
            'name'     => ['sometimes', 'string', 'max:150'],
            'ean'      => [
                'sometimes',
                'integer',
                Rule::unique('products')
                    ->ignore($this->product)
                    ->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            'chip_id'  => ['sometimes', 'integer'],
            'logo_url' => ['nullable', 'url'],
        ];
    }
}
