<?php

namespace App\Http\Requests\Shop;

use App\Enums\ShopType;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\ForbidsEmptyRequests;

class UpdateShopRequest extends FormRequest
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
            'customer_number' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('shops')
                    ->ignore($this->shop)
                    ->where(function ($query) {
                        return $query->whereNull('deleted_at');
                    })
            ],
            'name'            => ['sometimes', 'string', 'max:150'],
            'type'            => ['sometimes', Rule::in(array_column(ShopType::cases(), 'value'))],
            'street'          => ['sometimes', 'string', 'max:100'],
            'house_number'    => ['sometimes', 'string', 'max:10'],
            'postal_code'     => ['sometimes', 'postal_code:NL'],
            'city'            => ['sometimes', 'string', 'max:50'],
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

        if (in_array('postal_code', array_keys($validatedData))) {
            // Force a uniform postal code formatting.
            $postalCode = str_replace(' ', '', $this->postal_code);
            $postalCode = wordwrap($this->postal_code, strlen($this->postal_code) - 2, ' ', true);
            $postalCode = strtoupper($postalCode);

            $validatedData['postal_code'] = $postalCode;
        }

        return data_get($validatedData, $key, $default);
    }
}
