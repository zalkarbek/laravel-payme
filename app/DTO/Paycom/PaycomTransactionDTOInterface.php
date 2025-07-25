<?php

namespace App\DTO\Paycom;

use App\Http\Requests\Paycom\PaycomRequest;

interface PaycomTransactionDTOInterface
{
    public static function makeFromPaycomRequest(PaycomRequest $request): mixed;
}
