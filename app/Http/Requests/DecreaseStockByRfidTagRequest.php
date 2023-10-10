<?php

namespace App\Http\Requests;

use App\Models\Freezer;
use App\Models\RfidTag;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class DecreaseStockByRfidTagRequest extends FormRequest
{
    /**
     * The freezer that is being modified.
     *
     * @var Freezer
     */
    public $freezer;

    /**
     * The RFID tag that triggers that mutation.
     *
     * @var RfidTag
     */
    public $rfidTag;

    /**
     * The product the stock of which is being modified.
     *
     * @var Product
     */
    public $product;

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
            'freezer_id' => ['required', 'exists:freezers,id'],
            'rfid_tag'   => ['required', 'exists:rfid_tags,tag'],
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
            $this->freezer = Freezer::find($this->freezer_id);
            $this->rfidTag = RfidTag::find($this->rfid_tag);
            $this->product = $this->rfidTag->product;

            $this->freezer->load('products');

            if (!$this->freezer->products->contains($this->product)) {
                Log::stack(['stocks', 'sentry'])->notice('A decrese stock by RFID tag request has failed.', [
                    'request'  => $this->all(),
                    'error'    => trans('validation.custom.freezer.product_connection_missing'),
                ]);

                abort(422, trans('validation.custom.freezer.product_connection_missing'));
            }
        });
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function failedValidation(Validator $validator)
    {
        Log::stack(['stocks', 'sentry'])->notice('A decrese stock by RFID tag request has failed.', [
            'request'  => $this->all(),
            'errorBag' => $validator->getMessageBag(),
        ]);

        parent::failedValidation($validator);
    }
}
