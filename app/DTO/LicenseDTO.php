<?php

namespace App\DTO;

use DragonCode\SimpleDataTransferObject\DataTransferObject;

class LicenseDTO extends DataTransferObject {

    public int $user_id;
    public string $type;
    public int $active_from;
    public int $active_to;

}
