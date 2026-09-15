<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrencyExchange extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'date', 'holder_type', 'cashbox_id', 'bank_account_id',
        'paid_currency_id', 'paid_amount', 'paid_rate',
        'received_currency_id', 'received_amount', 'received_rate', 'description',
    ];

    protected $casts = ['date' => 'date'];

    public function cashbox() { return $this->belongsTo(Cashbox::class); }
    public function bankAccount() { return $this->belongsTo(BankAccount::class); }
    public function paidCurrency() { return $this->belongsTo(Currency::class, 'paid_currency_id'); }
    public function receivedCurrency() { return $this->belongsTo(Currency::class, 'received_currency_id'); }

    public function holderLabel(): string
    {
        return $this->holder_type === 'cashbox' ? $this->cashbox?->name : $this->bankAccount?->name;
    }

    /** Base-currency value difference between what was paid out and what was received in — a realized FX gain/loss. */
    public function gainLoss(): float
    {
        return round(($this->received_amount * $this->received_rate) - ($this->paid_amount * $this->paid_rate), 4);
    }
}
