<?php

namespace App\Services\Payments\Paycom;
use App\Models\Payment;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Support\Facades\URL;

class PaycomService
{
    protected Payment $payment;
    protected User $user;
    protected string $description;

    public function __construct(protected PaymentService $paymentService)
    {}

    public function prepareAndGetInstance(Payment $payment, User $user, ?string $description): static
    {
        $this->payment = $payment;
        $this->user = $user;
        $this->description = $description;
        return $this;
    }

    public function getPayUrl(): string
    {
        $merchantId = $this->getMerchantId();
        $paymentId = $this->payment->id;
        $paycomPayUrl= config('paycom.pay_url');

        $amount = $this->payment->sum;
        $paymentResultUrl = URL::temporarySignedRoute(
            'payment.paycom.result',
            now()->addHours(18),
            ['payment_id' => $paymentId]
        );

        $params = [
            'm' => $merchantId,
            'ac.payment_id' => $paymentId,
            'a' => $amount,
            'l' => app()->getLocale(),
            'c' => $paymentResultUrl,
            'ct' => config('paycom.ct'),
            'cr' => config('paycom.cr'),
        ];

        $raw = collect($params)
            ->map(fn($v, $k) => "$k=$v")
            ->implode(';');

        $encoded = base64_encode($raw);
        return "$paycomPayUrl/$encoded";
    }

    private function getMerchantId(): string
    {
        if($this->payment->isLicensePayment()) {
            return config('paycom.merchant_id_for_license_pay');
        }

        return config('paycom.merchant_id_for_event_pay');
    }
}
