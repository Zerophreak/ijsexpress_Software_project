<?php

namespace App\Http\Requests\Freezer;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreFreezerRequest extends FormRequest
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
            'name'       => [
                'max:200',
                Rule::requiredIf(function () {
                    return $this->shop->freezers()->whereNull('name')->exists();
                }),
                Rule::unique('freezers')->where(function ($query) {
                    return $query->whereNull('deleted_at')
                        ->where('shop_id', $this->shop->id);
                })
            ],
            'model_name' => ['required', 'max:200'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if ($validator->fails()) {
            return;
        }

        $validator->after(function () {
            if ($this->shop->freezers()->count() >= 10) {
                abort(422, 'A maximum of 10 freezers per shop is exceeded');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The :attribute field is required, since this store already has a freezer without a name.',
            'name.unique'   => 'This store already has a freezer with the same name.',
        ];
    }
}
