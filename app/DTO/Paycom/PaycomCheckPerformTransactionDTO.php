<?php

namespace App\DTO\Paycom;

use App\Http\Requests\Paycom\PaycomRequest;
use DragonCode\SimpleDataTransferObject\DataTransferObject;

class PaycomCheckPerformTransactionDTO extends DataTransferObject implements PaycomTransactionDTOInterface
{
    public int $requestId;
    public string $method;
    public int $amount;
    public int $paymentId;

    /**
     * @throws \ReflectionException
     */
    public static function makeFromPaycomRequest(PaycomRequest $request): static
    {
        return new static([
            'requestId' => (int)$request->requestId(),
            'method' => $request->rpcMethod(),
            'paymentId' => (int)$request->account('payment_id'),
            'amount' => (int)$request->params('amount'),
        ]);
    }
}
