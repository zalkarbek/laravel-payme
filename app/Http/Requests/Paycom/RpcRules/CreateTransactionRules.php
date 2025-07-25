<?php

namespace App\Http\Requests\Paycom\RpcRules;

use App\Exceptions\PaycomException;
use Illuminate\Contracts\Validation\Validator;

class CreateTransactionRules extends BaseRpcRules
{
    public static function rules(): array
    {
        return [
            ...parent::rules(),
            'params.id' => ['required', 'string'],
            'params.amount' => ['required', 'integer'],
            'params.time' => ['required', 'integer'],
            'params.account.payment_id' => ['required', 'string'],
        ];
    }

    public static function handleValidationFailure(int|string|null $requestId, Validator $validator): PaycomException
    {
        $errors = $validator->errors();
        $field = $errors->keys()[0] ?? null;

        if($field === 'params.amount') {
            return new PaycomException(
                $requestId,
                PaycomException::message(
                    'Неверная сумма платежа или неверный формат',
                    'To‘lov miqdori yoki formati noto‘g‘ri',
                    'Incorrect payment amount or wrong format'
                ),
                PaycomException::ERROR_INVALID_AMOUNT
            );
        }

        if($field === 'params.account.payment_id') {
            return new PaycomException(
                $requestId,
                PaycomException::message(
                    'Неверный код платежа или неверный формат',
                    'To‘lov kodi yoki format noto‘g‘ri',
                    'Incorrect payment code or wrong format'
                ),
                PaycomException::ERROR_INVALID_ACCOUNT,
                'payment_id'
            );
        }

        return parent::handleValidationFailure($requestId, $validator);
    }
}
