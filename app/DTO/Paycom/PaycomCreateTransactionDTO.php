<?php

namespace App\DTO\Paycom;

use App\Http\Requests\Paycom\PaycomRequest;
use DragonCode\SimpleDataTransferObject\DataTransferObject;

class PaycomCreateTransactionDTO extends DataTransferObject implements PaycomTransactionDTOInterface
{
    public int $requestId;
    public string $method;
    public int $paymentId;

    public string $transactionId;
    public int $amount;
    public int $time;

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
            'amount' => (int)$request->params('amount'),
            'time' => (int)$request->params('time'),
        ]);
    }
}
