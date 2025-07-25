<?php

namespace App\DTO;

use DragonCode\SimpleDataTransferObject\DataTransferObject;

class LicensePaymentDTO extends DataTransferObject {

    public int $user_id;
    public int $sum;

}
