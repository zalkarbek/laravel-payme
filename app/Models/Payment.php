<?php

namespace App\Models;

use App\Enums\PaymentTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'entity_payment_id',
        'entity_payment_type',
        'payment_method',
        'sum',
        'paid_at',
        'title',
        'gateway'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];


    public function getUpdatedAtColumn()
    {
        return null;
    }

    public function isPaid(): bool
    {
        return (bool)$this->paid_at;
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return bool
     */
    public function isEventPayment(): bool
    {

        return $this->entity_payment_type === PaymentTypeEnum::EventPayment->value;
    }

    /**
     * @return bool
     */
    public function isLicensePayment(): bool
    {
        return $this->entity_payment_type === PaymentTypeEnum::LicencePayment->value;
    }

    /**
     * @return bool
     */
    public function isOrderPayment()
    {

        return $this->entity_payment_type === 'order_payment';
    }

    /**
     * @return BelongsTo
     */
    public function eventPayment(): BelongsTo
    {

        return $this->belongsTo(EventPayment::class, 'entity_payment_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function licensePayment(): BelongsTo
    {

        return $this->belongsTo(LicensePayment::class, 'entity_payment_id', 'id');
    }

    public function paycomTransaction(): HasOne
    {
        return $this->hasOne(PaycomTransaction::class, 'payment_id', 'id');
    }
}
