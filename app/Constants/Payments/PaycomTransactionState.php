<?php

namespace App\Constants\Payments;

class PaycomTransactionState
{
    const STATE_CREATED = 1;
    const STATE_COMPLETED = 2;
    const STATE_CANCELLED = -1;
    const STATE_CANCELLED_AFTER_COMPLETE = -2;
}
