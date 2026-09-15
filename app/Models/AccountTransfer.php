<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'date', 'from_account_id', 'from_person_id', 'to_account_id', 'to_person_id',
        'amount', 'currency_id', 'fx_rate', 'description', 'journal_entry_id',
    ];

    protected $casts = ['date' => 'date'];

    public function fromAccount() { return $this->belongsTo(Account::class, 'from_account_id'); }
    public function fromPerson() { return $this->belongsTo(Person::class, 'from_person_id'); }
    public function toAccount() { return $this->belongsTo(Account::class, 'to_account_id'); }
    public function toPerson() { return $this->belongsTo(Person::class, 'to_person_id'); }
    public function currency() { return $this->belongsTo(Currency::class); }
    public function journalEntry() { return $this->belongsTo(JournalEntry::class); }

    public function fromLabel(): string
    {
        return $this->fromPerson?->name ?? $this->fromAccount?->name;
    }

    public function toLabel(): string
    {
        return $this->toPerson?->name ?? $this->toAccount?->name;
    }
}
