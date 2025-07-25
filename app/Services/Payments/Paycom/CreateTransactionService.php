<?php

namespace App\Services\Payments\Paycom;

use App\Constants\Payments\PaycomTransactionReason;
use App\Constants\Payments\PaycomTransactionState;
use App\Constants\Payments\PaymentGateways;
use App\DTO\Paycom\PaycomCreateTransactionDTO;
use App\Exceptions\PaycomException;
use App\Helpers\FormatHelper;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use App\Repositories\Payments\Paycom\PaycomTransactionRepository;

class CreateTransactionService
{
    public function __construct(
        protected PaymentRepository $paymentRepository,
        protected PaycomTransactionRepository $paycomTransactionRepository,
    )
    {}

    /**
     * @see PaycomController::__invoke
     *
     * @param PaycomCreateTransactionDTO $dto
     * @return array
     * @throws PaycomException
     *
     */
    public function run(PaycomCreateTransactionDTO $dto): array
    {
        $payment = $this->paymentRepository->findByIdAndGateway($dto->paymentId, PaymentGateways::PAYCOM_PAY);
        $this->validate($dto, $payment);
        $transaction = $this->paycomTransactionRepository->create($dto);

        return [
            'create_time' => FormatHelper::datetime2timestamp($transaction->create_time),
            'transaction' => (string)$transaction->id,
            'state' => $transaction->state,
            'receivers' => $transaction->receivers,
        ];
    }

    /**
     * @param PaycomCreateTransactionDTO $dto
     * @param Payment|null $payment
     * @return void
     * @throws PaycomException
     */
    private function validate(PaycomCreateTransactionDTO $dto, ?Payment $payment): void
    {
        $transactionExpired = config('paycom.transaction_expired');

        if (!$payment?->id) {
            throw new PaycomException(
                $dto->requestId,
                PaycomException::message(
                    'Неверный код код платежа.',
                    'Harid kodida xatolik.',
                    'Incorrect order code.'
                ),
                PaycomException::ERROR_INVALID_ACCOUNT,
                'payment_id'
            );
        }

        if ($payment->sum !== $dto->amount) {
            throw new PaycomException(
                $dto->requestId,
                PaycomException::message(
                    'Неверный сумма платежа.',
                    'Harid kodida xatolik.',
                    'Incorrect order code.'
                ),
                PaycomException::ERROR_INVALID_ACCOUNT,
                'amount'
            );
        }

        $transactionCreateTime = FormatHelper::timestamp2milliseconds(
            $dto->time
        ) - FormatHelper::timestamp(true);

        if ($transactionCreateTime >= $transactionExpired) {
            throw new PaycomException(
                $dto->paymentId,
                PaycomException::message(
                    'С даты создания транзакции прошло ' . $transactionExpired . 'мс',
                    'Tranzaksiya yaratilgan sanadan ' . $transactionExpired . 'ms o`tgan',
                    'Since create time of the transaction passed ' . $transactionExpired . 'ms'
                ),
                PaycomException::ERROR_INVALID_ACCOUNT,
                'time'
            );
        }

        $transaction = $this->paycomTransactionRepository->findByPaymentId($dto->paymentId);

        if (
            $transaction && (
                $transaction->state === PaycomTransactionState::STATE_CREATED
                || $transaction->state === PaycomTransactionState::STATE_COMPLETED
            )
            && $transaction->transaction_id !== $dto->transactionId
        ) {
            throw new PaycomException(
                $dto->paymentId,
                PaycomException::message(
                    'Для этого платежа существует другая созданная транзакция',
                    'Ushbu to‘lov uchun boshqa yaratilgan tranzaksiya mavjud',
                    'There is another created transaction for this payment'
                ),
                PaycomException::ERROR_INVALID_ACCOUNT,
            );
        }

        if (
            $transaction
            && $transaction->state === PaycomTransactionState::STATE_CREATED
            && $transaction->transaction_id === $dto->transactionId
        ) {
            throw new PaycomException(
                $dto->paymentId,
                PaycomException::message(
                    'Транзакция для данного платежа уже была создана',
                    'Ushbu to‘lov uchun tranzaksiya allaqachon yaratilgan',
                    'A transaction for this payment has already been created'
                ),
                PaycomException::ERROR_INVALID_ACCOUNT,
            );
        }

        if (
            $transaction
            && $transaction->state !== PaycomTransactionState::STATE_CREATED
        ) {
            throw new PaycomException(
                $dto->paymentId,
                PaycomException::message(
                    'Транзакция найдена, но не активна',
                    'Tranzaksiya topildi, lekin faol emas',
                    'Transaction found, but is not active'
                ),
                PaycomException::ERROR_INVALID_ACCOUNT,
            );
        }

        if ($transaction && $transaction->isTransactionExpired()) {
            $transaction->cancelTransaction(
                PaycomTransactionReason::REASON_CANCELLED_BY_TIMEOUT
            );

            throw new PaycomException(
                $dto->paymentId,
                PaycomException::message(
                    'Транзакция просрочена',
                    'Tranzaksiya muddati o‘tgan',
                    'Transaction is expired'
                ),
                PaycomException::ERROR_COULD_NOT_PERFORM,
            );
        }
    }
}
