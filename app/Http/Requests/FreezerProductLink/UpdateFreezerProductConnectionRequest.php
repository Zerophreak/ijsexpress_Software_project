<?php

namespace App\Http\Requests\FreezerProductLink;

use App\Enums\MutationType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Rules\DoesNotExceedMaximumStock;
use App\Rules\IsNotLessThanCurrentStock;
use App\Rules\ForbidsNegativeCurrentStock;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateFreezerProductConnectionRequest extends FormRequest
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
            'current_stock' => [
                'numeric',
                'gt:0',
                'lte:200',
                Rule::prohibitedIf(function () {
                    return in_array($this->mutation_type, ['sales', 'filling']);
                }),
                Rule::requiredIf(function () {
                    return $this->mutation_type && !$this->alteration;
                }),
                new DoesNotExceedMaximumStock($this->freezer, $this->product, $this->max_stock)
            ],

            'alteration' => [
                'numeric',
                'between:-100,100',
                Rule::prohibitedIf(function () {
                    return $this->current_stock;
                }),
                Rule::requiredIf(function () {
                    return $this->mutation_type && !$this->current_stock;
                }),
                Rule::when(function () {
                    return $this->mutation_type === 'filling';
                }, 'gte:1'),
                Rule::when(function () {
                    return $this->mutation_type === 'sales';
                }, 'lte:-1'),
                new DoesNotExceedMaximumStock($this->freezer, $this->product, $this->max_stock),
                new ForbidsNegativeCurrentStock($this->freezer, $this->product)
            ],

            'max_stock'     => [
                'numeric',
                'gt:0',
                'lte:200',
                new IsNotLessThanCurrentStock($this->freezer, $this->product, $this->current_stock, $this->alteration)
            ],

            'mutation_type' => [Rule::requiredIf(function () { return $this->alteration; }), Rule::in(array_column(MutationType::cases(), 'value'))]
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->freezer->load('products');

        if (!$this->freezer->products->contains($this->product)) {
            abort(422, trans('validation.custom.freezer.product_connection_missing'));
        }
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'current_stock.prohibited' => "The current stock may only be provided with a 'correction' mutation type.",
            'alteration.prohibited'    => 'The alteration cannot be provided together with the current stock.',
            'alteration.gte'           => 'The alteration must be greater than or equal to :value for the filling mutation type.',
            'alteration.lte'           => 'The alteration must be less than or equal to :value for the sales mutation type.',
            'mutation_type.required'   => 'The mutation type is required when an alteration is provided.',
            'alteration.required'      => 'Please provide either an alteration or the current stock when providing a mutation type.',
            'current_stock.required'   => 'Please provide either an alteration or the current stock when providing a mutation type.'
        ];
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
        Log::stack(['stocks', 'sentry'])->notice('A freezer product connection update request has failed.', [
            'request'  => $this->all(),
            'errorBag' => $validator->getMessageBag(),
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    protected function withValidator($validator): void
    {
        if ($validator->fails()) {
            return;
        }

        $validator->after(function (Validator $validator) {
            if (empty($validator->validated())) {
                Log::stack(['stocks', 'sentry'])->notice('An unusable freezer product connection update request has been recieved.', [
                    'request'  => $this->all(),
                ]);

                abort(422, 'No usable data provided with the request.');
            }

            if ($validator->errors()->isNotEmpty()) {
                return $this->failedValidation($validator);
            }
        });
    }
}
