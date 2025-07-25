<?php

namespace App\Enums;

enum PaymentTypeEnum: string {
    case EventPayment = 'event_payment';
    case LicencePayment = 'license_payment';

    public function label(): string
    {
        return match($this) {
            self::EventPayment => 'Оплата дистанции',
            self::LicencePayment => 'Оплата лицензии',
        };
    }
}
