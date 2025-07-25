<?php

namespace App\Models;

use App\Constants\Payments\PaycomTransactionState;
use App\Helpers\FormatHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaycomTransaction extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'create_time_timestamp',
        'create_time',
        'perform_time',
        'cancel_time',
        'sum',
        'state',
        'reason',
        'receivers'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'datetime',
        'create_time' => 'datetime',
        'perform_time' => 'datetime',
        'cancel_time' => 'datetime',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }

    /**
     * Проверяем не устарел ли транзакция
     * @return bool
     */
    public function isTransactionExpired(): bool
    {
        return $this->state == PaycomTransactionState::STATE_CREATED
            && abs(
                FormatHelper::datetime2timestamp($this->create_time)
                - FormatHelper::timestamp(true)
            ) > config('paycom.transaction_expired');
    }

    /**
     * Отмена транзакции
     * @param int|null $reason
     * @return void
     */
    public function cancelTransaction(?int $reason): void
    {
        $this->cancel_time = FormatHelper::timestamp2datetime(FormatHelper::timestamp());

        if ($this->state == PaycomTransactionState::STATE_COMPLETED) {
            $this->state = PaycomTransactionState::STATE_CANCELLED_AFTER_COMPLETE;

        } else {
            $this->state = PaycomTransactionState::STATE_CANCELLED;
        }

        $this->reason = $reason;
        $this->save();
    }
}
