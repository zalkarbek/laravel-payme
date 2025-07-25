<?php

namespace App\DTO\Paycom\Factories;

use App\DTO\Paycom\PaycomTransactionDTOInterface;
use App\Http\Requests\Paycom\PaycomRequest;

class PaycomDTOFactory
{
    /**
     * @param PaycomRequest $request
     * @return PaycomTransactionDTOInterface|null
     */
    public function makeFromPaycomRequest(PaycomRequest $request): ?PaycomTransactionDTOInterface
    {
        $class = $this->resolveDtoClass($request->rpcMethod());

        if (!$class || !is_subclass_of($class, PaycomTransactionDTOInterface::class)) {
            return null;
        }

        return $class::makeFromPaycomRequest($request);
    }

    /**
     * @return class-string<PaycomTransactionDTOInterface>|null
     */
    private function resolveDtoClass(string $rpcMethod): ?string
    {
        if (!$rpcMethod) {
            return null;
        }

        $class = 'App\\DTO\\Paycom\\'. 'Paycom' . ucfirst($rpcMethod) . 'DTO';
        return class_exists($class) ? $class : null;
    }
}
