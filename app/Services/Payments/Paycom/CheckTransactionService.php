<?php

namespace App\Services\Payments\Paycom;

use App\DTO\Paycom\PaycomCheckTransactionDTO;
use App\Exceptions\PaycomException;
use App\Helpers\FormatHelper;
use App\Models\PaycomTransaction;
use App\Repositories\Payments\Paycom\PaycomTransactionRepository;

class CheckTransactionService
{

    public function __construct(
        protected PaycomTransactionRepository $paycomTransactionRepository,
    )
    {}

    /**
     * @see PaycomController::__invoke
     *
     * @param PaycomCheckTransactionDTO $dto
     * @return array
     * @throws PaycomException
     *
     */
    public function run(PaycomCheckTransactionDTO $dto): array
    {
        $transaction = $this->paycomTransactionRepository->findByTransactionId($dto->transactionId);
        $this->validate($dto, $transaction);

        return [
            ...array_filter([
                'create_time' => $transaction->create_time
                    ? FormatHelper::datetime2timestamp($transaction->create_time) : 0,
                'perform_time' => $transaction->perform_time
                    ? FormatHelper::datetime2timestamp($transaction->perform_time) : 0,
                'cancel_time' => $transaction->cancel_time
                    ? FormatHelper::datetime2timestamp($transaction->cancel_time) : 0,

            ], fn($v) => $v !== null),

            'transaction' => (string)$transaction->id,
            'state' => $transaction->state,

            'reason' => $transaction->reason
                ? (int)$transaction->reason : null,
        ];
    }

    /**
     * @param PaycomCheckTransactionDTO $dto
     * @param PaycomTransaction|null $transaction
     * @return void
     * @throws PaycomException
     */
    private function validate(PaycomCheckTransactionDTO $dto, ?PaycomTransaction $transaction): void
    {
        if (!$transaction) {
            throw new PaycomException(
                $dto->requestId,
                PaycomException::message(
                    'Транзакция не найдена',
                    'Tranzaksiya topilmadi',
                    'Transaction not found'
                ),
                PaycomException::ERROR_TRANSACTION_NOT_FOUND,
                null
            );
        }
    }
}
