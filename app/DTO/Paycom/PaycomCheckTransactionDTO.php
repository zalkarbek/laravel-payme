<?php

namespace App\DTO\Paycom;

use App\Http\Requests\Paycom\PaycomRequest;
use DragonCode\SimpleDataTransferObject\DataTransferObject;

class PaycomCheckTransactionDTO extends DataTransferObject implements PaycomTransactionDTOInterface
{
    public int $requestId;
    public string $method;
    public string $transactionId;
    public ?int $paymentId;

    /**
     * @throws \ReflectionException
     */
    public static function makeFromPaycomRequest(PaycomRequest $request): static
    {
        return new static([
            'requestId' => (int)$request->requestId(),
            'method' => $request->rpcMethod(),
            'paymentId' => (int)$request->account('payment_id'),
            'transactionId' => $request->params('id'),
        ]);
    }
}
