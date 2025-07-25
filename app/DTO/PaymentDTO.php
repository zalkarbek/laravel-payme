<?php

namespace App\DTO;

use DragonCode\SimpleDataTransferObject\DataTransferObject;

class PaymentDTO extends DataTransferObject
{
    public ?int $user_id;
    public string $shop_transaction_id;
    public string $entity_payment_type;
    public int $entity_payment_id;
    public string $title;
    public int $sum;
    public string $gateway;
}
