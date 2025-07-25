<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class PaycomException extends Exception
{
    const ERROR_INTERNAL_SYSTEM = -32400;
    const ERROR_INSUFFICIENT_PRIVILEGE = -32504;
    const ERROR_INVALID_JSON_RPC_OBJECT = -32600;
    const ERROR_METHOD_NOT_FOUND = -32601;
    const ERROR_INVALID_AMOUNT = -31001;
    const ERROR_TRANSACTION_NOT_FOUND = -31003;
    const ERROR_INVALID_ACCOUNT = -31050;
    const ERROR_COULD_NOT_CANCEL = -31007;
    const ERROR_COULD_NOT_PERFORM = -31008;

    public int $requestId;
    public array $error;
    public array|string|null $data;
    public array $messages = [];

    public function __construct(int $requestId, array $messages, int $code, string $data = null)
    {
        $this->requestId = $requestId;
        $this->messages = $messages;
        $this->code = $code;
        $this->data = $data;

        $this->error = [
            'code' => $this->code
        ];

        $this->error['message'] = $this->messages;

        if ($this->data) {
            $this->error['data'] = $this->data;
        }

        parent::__construct(Arr::get($this->messages, 'en'), $this->code);
    }

    public function report(): bool
    {
        Log::channel('paycom')->error('Paycom Exception', [
            'request_id' => $this->requestId,
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
            'messages' => $this->messages,
            'data' => $this->data,
            'trace' => $this->getTraceAsString(),
        ]);

        return false;
    }

    public function responseError(): array
    {
        return array_filter(
            [
                'id' => $this->requestId,
                'error' => $this->error,
            ],
            fn($v) => $v !== null
        );
    }

    public static function message($ru, $uz = '', $en = ''): array
    {
        return [
            'ru' => $ru,
            'uz' => $uz,
            'en' => $en
        ];
    }

    public static function manualReport(\Exception $exception, int $requestId): void
    {
        Log::channel('paycom')->error('Paycom Exception', [
            'request_id' => $requestId,
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
