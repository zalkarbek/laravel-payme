<?php

namespace App\Services\Payments\Paycom;

use App\Constants\Payments\PaymentGateways;
use App\DTO\Paycom\PaycomCheckPerformTransactionDTO;
use App\Exceptions\PaycomException;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use App\Repositories\Payments\Paycom\PaycomTransactionRepository;

class CheckPerformTransactionService
{
    public function __construct(
        protected PaymentRepository $paymentRepository,
        protected PaycomTransactionRepository $paycomTransactionRepository,
    )
    {}

    /**
     * @see PaycomController::__invoke
     *
     * @param PaycomCheckPerformTransactionDTO $dto
     * @return array
     * @throws PaycomException
     */
    public function run(PaycomCheckPerformTransactionDTO $dto): array
    {
        $payment = $this->paymentRepository->findByIdAndGateway($dto->paymentId, PaymentGateways::PAYCOM_PAY);
        $this->validate($dto, $payment);

        return [
            'allow' => true
        ];
    }

    /**
     * @param PaycomCheckPerformTransactionDTO $dto
     * @param Payment|null $payment
     * @return void
     * @throws PaycomException
     */
    protected function validate(PaycomCheckPerformTransactionDTO $dto, ?Payment $payment): void
    {
        if (!$payment) {
            throw new PaycomException(
                $dto->requestId,
                PaycomException::message(
                    'Неверный код платежа.',
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
                PaycomException::ERROR_INVALID_ACCOUNT
            );
        }

        $transaction = $this->paycomTransactionRepository->findByPaymentId($payment->id);

        if($transaction && $transaction->state !== null) {
            throw new PaycomException(
                $dto->requestId,
                PaycomException::message(
                    'Невозможно выполнить платеж, для данного платежа уже существует другая транзакция',
                    'To‘lovni amalga oshirib bo‘lmaydi, bu to‘lov uchun allaqachon boshqa tranzaksiya mavjud',
                    'Cannot process payment, another transaction already exists for this payment'
                ),
                PaycomException::ERROR_INVALID_ACCOUNT,
                'payment_id'
            );
        }
    }
}
