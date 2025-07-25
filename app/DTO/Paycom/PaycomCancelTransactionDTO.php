<?php

namespace App\DTO\Paycom;

use App\Http\Requests\Paycom\PaycomRequest;
use DragonCode\SimpleDataTransferObject\DataTransferObject;

class PaycomCancelTransactionDTO extends DataTransferObject implements PaycomTransactionDTOInterface
{
    public int $requestId;
    public string $method;
    public string $transactionId;
    public ?int $reason;

    public function __construct(array $items = [])
    {
        parent::__construct($items);
    }

    /**
     * @throws \ReflectionException
     */
    public static function makeFromPaycomRequest(PaycomRequest $request): static
    {
        return new static([
            'requestId' => (int)$request->requestId(),
            'method' => $request->rpcMethod(),
            'transactionId' => $request->params('id'),
            'reason' => $request->params('reason'),
        ]);
    }
}
