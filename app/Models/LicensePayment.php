<?php

namespace App\Models;

use App\Constants\EntityFields\LicensePaymentFields;
use App\Contracts\Payments\EntityPaymentInterface;
use App\Traits\EntityPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 */
class LicensePayment extends Model implements EntityPaymentInterface {
    use HasFactory, SoftDeletes, EntityPayment;

    /**
     * @var array
     */
    protected $fillable = [
        LicensePaymentFields::USER_ID,
        LicensePaymentFields::SUM,
        LicensePaymentFields::PAID_AT,
    ];

    /**
     * @var string
     */
    protected string $entityPaymentType = 'license_payment';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user():\Illuminate\Database\Eloquent\Relations\BelongsTo
    {

        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function licenseRequest()
    {
        return $this->hasOne(LicenseRequest::class, 'license_payment_id', 'id');
    }

}
