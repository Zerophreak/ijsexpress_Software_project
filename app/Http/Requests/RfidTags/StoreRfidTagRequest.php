<?php

namespace App\Http\Requests\RfidTags;

use Illuminate\Foundation\Http\FormRequest;

class StoreRfidTagRequest extends FormRequest
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
            'tag'        => ['required', 'unique:rfid_tags', 'min:3', 'max:100'],
            'product_id' => ['required', 'exists:products,id']
        ];
    }
}
