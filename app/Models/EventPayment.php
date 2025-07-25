<?php

namespace App\Models;

use App\Contracts\Payments\EntityPaymentInterface;
use App\Traits\EntityPayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class EventPayment extends Model implements EntityPaymentInterface
{

    use HasFactory, EntityPayment;


    /**
     * @var string
     */
    protected $entityPaymentType = 'event_payment';

    /**
     * @var string
     */
    protected $table = 'event_payments';

    /**
     * @var string[]
     */
    protected $casts = [
        'paid_at' => 'datetime'
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'event_id',
        'user_id',
        'sum',
        'event_type',
        'payment_method',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user() {

        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function event()
    {
        return $this->hasOne(Event::class, 'id', 'event_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'entity_payment_id', 'id');
    }

    /**
     * @return null
     */
    public function getUpdatedAtColumn()
    {
        return NULL;
    }

    /**
     * @return string
     */
    public function getEntityPaymentType()
    :string {

        return $this->entityPaymentType;
    }

}
