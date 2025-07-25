<?php

namespace App\Services\Payments\Paycom;

use App\Constants\Payments\PaycomTransactionState;
use App\DTO\Paycom\PaycomCancelTransactionDTO;
use App\Exceptions\PaycomException;
use App\Helpers\FormatHelper;
use App\Models\PaycomTransaction;
use App\Models\Payment;
use App\Repositories\Payments\Paycom\PaycomTransactionRepository;

class CancelTransactionService
{
    public function __construct(
        protected PaycomTransactionRepository $paycomTransactionRepository,
    )
    {}

    /**
     * @see PaycomController::__invoke
     *
     * @param PaycomCancelTransactionDTO $dto
     * @return array
     * @throws PaycomException
     *
     */
    public function run(PaycomCancelTransactionDTO $dto): array
    {
        $transaction = $this->paycomTransactionRepository->findByTransactionId($dto->transactionId);
        $this->validate($dto, $transaction);

        return match ($transaction->state) {

            PaycomTransactionState::STATE_CANCELLED,
            PaycomTransactionState::STATE_CANCELLED_AFTER_COMPLETE => $this->sendCancelResponse($transaction),

            PaycomTransactionState::STATE_COMPLETED,
            PaycomTransactionState::STATE_CREATED => $this->cancelTransaction($transaction, $dto),

            default => $this->handleTransactionCannotCancel($dto),
        };
    }


    /**
     * @param PaycomCancelTransactionDTO $dto
     * @param Payment|null $payment
     * @return void
     * @throws PaycomException
     */
    private function validate(PaycomCancelTransactionDTO $dto, ?Payment $payment): void
    {
        if (!$payment) {
           $this->handleTransactionNotFound($dto);
        }
    }

    /**
     * @param PaycomCancelTransactionDTO $dto
     * @return void
     * @throws PaycomException
     */
    private function handleTransactionNotFound(PaycomCancelTransactionDTO $dto): void
    {
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

    /**
     * @param PaycomCancelTransactionDTO $dto
     * @return void
     * @throws PaycomException
     */
    private function handleTransactionCannotCancel(PaycomCancelTransactionDTO $dto): void
    {
        throw new PaycomException(
            $dto->requestId,
            PaycomException::message(
                'Невозможно отменить транзакцию, статус транзакции не определен',
                'Tranzaksiyani bekor qilib bo‘lmaydi, tranzaksiya holati aniqlanmagan',
                'Cannot cancel transaction, transaction status is undefined'
            ),
            PaycomException::ERROR_COULD_NOT_CANCEL,
            null
        );
    }

    /**
     * @param PaycomTransaction $transaction
     * @return array
     */
    private function sendCancelResponse(PaycomTransaction $transaction): array
    {
        return [
            'transaction' => (string)$transaction->id,
            'cancel_time' => FormatHelper::datetime2timestamp($transaction->cancel_time),
            'state' => $transaction->state,
        ];
    }

    /**
     * @param PaycomTransaction $transaction
     * @param PaycomCancelTransactionDTO $dto
     * @return array
     */
    private function cancelTransaction(PaycomTransaction $transaction, PaycomCancelTransactionDTO $dto): array
    {
        $transaction->cancelTransaction($dto->reason);
        return $this->sendCancelResponse($transaction);
    }
}
