<?php

namespace App\Http\Requests\Paycom\RpcRules;

use App\Exceptions\PaycomException;
use Illuminate\Contracts\Validation\Validator;

interface RpcRulesInterface
{
    public static function rules(): array;

    public static function handleValidationFailure(int|string|null $requestId, Validator $validator): PaycomException;
}
