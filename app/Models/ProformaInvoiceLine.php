<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformaInvoiceLine extends Model
{
    use HasFactory;

    protected $fillable = ['proforma_invoice_id', 'item_id', 'quantity', 'unit_price', 'total'];

    public function proformaInvoice() { return $this->belongsTo(ProformaInvoice::class); }
    public function item() { return $this->belongsTo(Item::class); }
}
