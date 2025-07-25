<?php

namespace App\Repositories\Payments\Paycom;

use App\Constants\Payments\PaycomTransactionState;
use App\DTO\Paycom\PaycomCreateTransactionDTO;
use App\Helpers\FormatHelper;
use App\Models\PaycomTransaction;

class PaycomTransactionRepository
{
    public function __construct()
    {}

    /**
     * @param string $transactionId
     * @return PaycomTransaction|null
     */
    public function findByTransactionId(string $transactionId): ?PaycomTransaction
    {
        return PaycomTransaction::query()
            ->where('transaction_id', $transactionId)
            ->first();
    }

    /**
     * @param int $paymentId
     * @return PaycomTransaction|null
     */
    public function findByPaymentId(int $paymentId): ?PaycomTransaction
    {
        return PaycomTransaction::query()
            ->where('payment_id', $paymentId)
            ->first();
    }

    /**
     * @param PaycomCreateTransactionDTO $dto
     * @return PaycomTransaction
     */
    public function create(PaycomCreateTransactionDTO $dto): PaycomTransaction
    {
        $transaction = new PaycomTransaction();
        $transaction->payment_id = $dto->paymentId;
        $transaction->transaction_id = $dto->transactionId;
        $transaction->create_time_timestamp = $dto->time;
        $transaction->create_time = FormatHelper::timestamp2datetime($dto->time);
        $transaction->state = PaycomTransactionState::STATE_CREATED;
        $transaction->sum = $dto->amount;
        $transaction->save();
        return $transaction;
    }
}
