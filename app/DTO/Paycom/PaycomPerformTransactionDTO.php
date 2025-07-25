<?php

namespace App\DTO\Paycom;

use App\Http\Requests\Paycom\PaycomRequest;
use DragonCode\SimpleDataTransferObject\DataTransferObject;

class PaycomPerformTransactionDTO extends DataTransferObject implements PaycomTransactionDTOInterface
{
    public int $requestId;
    public string $method;
    public string $transactionId;

    /**
     * @throws \ReflectionException
     */
    public static function makeFromPaycomRequest(PaycomRequest $request): static
    {
        return new static([
            'requestId' => (int)$request->requestId(),
            'method' => $request->rpcMethod(),
            'transactionId' => $request->params('id'),
        ]);
    }
}
