<?php

namespace App\Http\Requests\Freezer;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\ForbidsEmptyRequests;

class UpdateFreezerRequest extends FormRequest
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
            'name'       => [
                'sometimes',
                'max:200',
                Rule::unique('freezers')->where(function ($query) {
                    return $query->whereNull('deleted_at')
                        ->where('shop_id', $this->freezer->shop_id);
                })
                ->ignore($this->freezer)
            ],
            'model_name' => ['sometimes', 'string', 'max:200'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.unique'   => 'This store already has a freezer with the same name.',
        ];
    }
}
