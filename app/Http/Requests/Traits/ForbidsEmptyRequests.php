<?php

namespace App\Http\Requests\Traits;

use Illuminate\Validation\Validator;

trait ForbidsEmptyRequests
{
    /**
     * Throw an error if no valuable data has been sent with the request.
     *
     * @param  mixed  $validator
     * @return void
     */
    public function after($validator)
    {
        if (empty($validator->validated())) {
            abort(422, 'No usable data provided with the request.');
        }
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

        $validator->after(function (Validator $validator) {
            $this->after($validator);
        });
    }
}
