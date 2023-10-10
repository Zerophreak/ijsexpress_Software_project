<?php

namespace App\Http\Requests\Mutations;

use Illuminate\Foundation\Http\FormRequest;

class GetMutationsPerFreezerPerProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (!$this->freezer->product($this->product)) {
            abort(404, trans('validation.custom.freezer.product_connection_missing'));
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [];
    }
}
