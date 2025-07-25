<?php

namespace App\DTO;

use DragonCode\SimpleDataTransferObject\DataTransferObject;

class EventPaymentDTO extends DataTransferObject {

    public int $event_id;

    public int $user_id;

    public int $sum;

    public string $event_type;
}
