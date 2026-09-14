<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashVoucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'type', 'date', 'cashbox_id', 'bank_account_id', 'person_id',
        'amount', 'currency_id', 'fx_rate', 'description', 'journal_entry_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function cashbox()
    {
        return $this->belongsTo(Cashbox::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }
}
