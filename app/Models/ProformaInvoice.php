<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformaInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'number', 'date', 'customer_name', 'customer_phone', 'customer_mobile',
        'warehouse_id', 'currency_id', 'fx_rate', 'discount', 'total_amount', 'notes',
    ];

    protected $casts = ['date' => 'date'];

    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function currency() { return $this->belongsTo(Currency::class); }
    public function lines() { return $this->hasMany(ProformaInvoiceLine::class); }
}
