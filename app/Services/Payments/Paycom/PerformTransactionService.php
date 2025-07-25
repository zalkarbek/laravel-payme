<?php

namespace App\Services\Payments\Paycom;

use App\Constants\Payments\PaycomTransactionReason;
use App\Constants\Payments\PaycomTransactionState;
use App\Constants\Payments\PaymentGateways;
use App\DTO\Paycom\PaycomPerformTransactionDTO;
use App\Events\Payment\PaymentPaid;
use App\Exceptions\PaycomException;
use App\Helpers\FormatHelper;
use App\Models\PaycomTransaction;
use App\Repositories\Payments\Paycom\PaycomTransactionRepository;

class PerformTransactionService
{
    public function __construct(
        protected PaycomTransactionRepository $paycomTransactionRepository
    )
    {}

    /**
     * @see PaycomController::__invoke
     *
     * @param PaycomPerformTransactionDTO $dto
     * @return array
     * @throws PaycomException
     *
     */
    public function run(PaycomPerformTransactionDTO $dto): array
    {
        $transaction = $this->paycomTransactionRepository->findByTransactionId($dto->transactionId);
        $this->validate($dto, $transaction);

        return match ($transaction->state) {
            PaycomTransactionState::STATE_CREATED => $this->handleActiveTransaction($transaction),
            PaycomTransactionState::STATE_COMPLETED => $this->handleCompletedTransaction($transaction),
            default => $this->handleTransactionUnknown($dto),
        };
    }

    /**
     * @param PaycomPerformTransactionDTO $dto
     * @param PaycomTransaction|null $transaction
     * @return void
     * @throws PaycomException
     */
    private function validate(PaycomPerformTransactionDTO $dto, ?PaycomTransaction $transaction): void
    {
        if (!$transaction) {
            throw new PaycomException(
                $dto->requestId,
                PaycomException::message(
                    'Транзакция не найдена',
                    'Tranzaksiya topilmadi',
                    'Transaction not found'
                ),
                PaycomException::ERROR_TRANSACTION_NOT_FOUND
            );
        }

        if ($transaction->isTransactionExpired()) {
            $transaction->cancelTransaction(PaycomTransactionReason::REASON_CANCELLED_BY_TIMEOUT);
            $this->handleTransactionExpired($dto);
        }
    }

    /**
     * @param PaycomTransaction $transaction
     * @return array
     */
    private function handleActiveTransaction(PaycomTransaction $transaction): array
    {
        $performTime = FormatHelper::timestamp(true);
        $transaction->state = PaycomTransactionState::STATE_COMPLETED;
        $transaction->perform_time = FormatHelper::timestamp2datetime($performTime);
        $transaction->save();
        PaymentPaid::dispatch($transaction->payment_id, PaymentGateways::PAYCOM_PAY);

        return [
            'transaction' => (string)$transaction->id,
            'perform_time' => $performTime,
            'state' => $transaction->state,
        ];
    }

    /**
     * @param PaycomTransaction $transaction
     * @return array
     */
    private function handleCompletedTransaction(PaycomTransaction $transaction): array
    {
        return [
            'transaction' => (string)$transaction->id,
            'perform_time' => FormatHelper::datetime2timestamp($transaction->perform_time),
            'state' => $transaction->state,
        ];
    }

    /**
     * @param PaycomPerformTransactionDTO $dto
     * @return void
     * @throws PaycomException
     */
    private function handleTransactionUnknown(PaycomPerformTransactionDTO $dto): void
    {
        throw new PaycomException(
            $dto->requestId,
            PaycomException::message(
                'Не удалось выполнить операцию',
                'Amalni bajarmadim',
                'Could not perform this operation'
            ),
            PaycomException::ERROR_COULD_NOT_PERFORM
        );
    }

    /**
     * @param PaycomPerformTransactionDTO $dto
     * @return void
     * @throws PaycomException
     */
    private function handleTransactionExpired(PaycomPerformTransactionDTO $dto): void
    {
        throw new PaycomException(
            $dto->requestId,
            PaycomException::message(
                'Транзакция просрочена',
                'Tranzaksiya muddati o‘tgan',
                'Transaction is expired'
            ),
            PaycomException::ERROR_COULD_NOT_PERFORM
        );
    }
}
