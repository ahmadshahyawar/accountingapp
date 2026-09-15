<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'date', 'from_type', 'from_cashbox_id', 'from_bank_account_id',
        'to_type', 'to_cashbox_id', 'to_bank_account_id',
        'amount', 'currency_id', 'fx_rate', 'description', 'journal_entry_id',
    ];

    protected $casts = ['date' => 'date'];

    public function fromCashbox() { return $this->belongsTo(Cashbox::class, 'from_cashbox_id'); }
    public function fromBankAccount() { return $this->belongsTo(BankAccount::class, 'from_bank_account_id'); }
    public function toCashbox() { return $this->belongsTo(Cashbox::class, 'to_cashbox_id'); }
    public function toBankAccount() { return $this->belongsTo(BankAccount::class, 'to_bank_account_id'); }
    public function currency() { return $this->belongsTo(Currency::class); }
    public function journalEntry() { return $this->belongsTo(JournalEntry::class); }

    public function fromLabel(): string
    {
        return $this->from_type === 'cashbox' ? $this->fromCashbox?->name : $this->fromBankAccount?->name;
    }

    public function toLabel(): string
    {
        return $this->to_type === 'cashbox' ? $this->toCashbox?->name : $this->toBankAccount?->name;
    }
}
