<?php

namespace App\Http\Requests\Paycom\RpcRules;

use App\Exceptions\PaycomException;
use Illuminate\Contracts\Validation\Validator;

class CheckTransactionRules extends BaseRpcRules
{
    public static function rules(): array
    {
        return [
            ...parent::rules(),
            'params.id' => ['required', 'string'],
        ];
    }

    public static function handleValidationFailure(int|string|null $requestId, Validator $validator): PaycomException
    {
        return parent::handleValidationFailure($requestId, $validator);
    }
}
