<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'date', 'person_id', 'warehouse_id', 'currency_id', 'fx_rate',
        'sales_invoice_id', 'total_amount', 'journal_entry_id', 'notes',
    ];

    protected $casts = ['date' => 'date'];

    public function customer() { return $this->belongsTo(Person::class, 'person_id'); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function currency() { return $this->belongsTo(Currency::class); }
    public function salesInvoice() { return $this->belongsTo(SalesInvoice::class); }
    public function journalEntry() { return $this->belongsTo(JournalEntry::class); }
    public function lines() { return $this->hasMany(SalesReturnLine::class); }
}
