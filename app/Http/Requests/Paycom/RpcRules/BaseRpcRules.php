<?php

namespace App\Http\Requests\Paycom\RpcRules;

use App\Constants\Payments\PaycomMerchantMethods;
use App\Exceptions\PaycomException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

abstract class BaseRpcRules implements RpcRulesInterface
{
    const RPC_VERSION = '2.0';
    public static function rules(): array
    {
        return [
            'jsonrpc' => ['required', Rule::in([static::RPC_VERSION])],
            'id' => ['required', 'integer'],
            'method' => ['required', 'string', Rule::in(PaycomMerchantMethods::METHOD_LISTS)],
        ];
    }

    public static function handleValidationFailure(int|string|null $requestId, Validator $validator): PaycomException
    {
        $errors = $validator->errors();
        $field = $errors->keys()[0] ?? null;

        if($field === 'method') {
            return new PaycomException(
                $requestId,
                PaycomException::message(
                    "Метод не найден",
                    "Usul topilmadi",
                    "Method not found"
                ),
                PaycomException::ERROR_METHOD_NOT_FOUND,
                $field
            );
        }

        return new PaycomException(
            $requestId,
            PaycomException::message(
                "Отсутствуют обязательные поля в RPC-запросе или тип полей не соответствует спецификации",
                "RPC so‘rovida majburiy maydonlar yo‘q yoki maydonlar turlari spetsifikatsiyaga mos kelmaydi",
                "Required fields are missing in the RPC request or field types do not match the specification"
            ),
            PaycomException::ERROR_INVALID_JSON_RPC_OBJECT,
            $field
        );
    }
}
