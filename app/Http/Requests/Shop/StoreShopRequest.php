<?php

namespace App\Http\Requests\Shop;

use App\Enums\ShopType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreShopRequest extends FormRequest
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
            'customer_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('shops')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                })
            ],
            'name'            => ['required', 'string', 'max:150'],
            'type'            => ['required', Rule::in(array_column(ShopType::cases(), 'value'))],
            'street'          => ['required', 'string', 'max:100'],
            'house_number'    => ['required', 'string', 'max:10'],
            'postal_code'     => ['required', 'postal_code:NL'],
            'city'            => ['required', 'string', 'max:50'],
            'logo_url'        => ['nullable', 'url'],
        ];
    }

    /**
     * Get the validated data from the request.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function validated($key = null, $default = null)
    {
        $validatedData = $this->validator->validated();

        // Force a uniform postal code formatting.
        $postalCode = str_replace(' ', '', $this->postal_code);
        $postalCode = wordwrap($this->postal_code, strlen($this->postal_code) - 2, ' ', true);
        $postalCode = strtoupper($postalCode);

        $validatedData['postal_code'] = $postalCode;

        return data_get($validatedData, $key, $default);
    }
}
