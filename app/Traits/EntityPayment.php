<?php

namespace App\Traits;

/**
 *
 */
trait EntityPayment
{
    /**
     * @return string
     */
    public function getEntityPaymentType():string {
        return $this->entityPaymentType;
    }

    /**
     * @return string
     */
    public function getEntityPaymentId():string {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isPaid():bool {
        return !is_null($this->paid_at);
    }
}
