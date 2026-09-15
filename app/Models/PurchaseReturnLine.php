<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnLine extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_return_id', 'item_id', 'quantity', 'unit_price', 'total'];

    public function purchaseReturn() { return $this->belongsTo(PurchaseReturn::class); }
    public function item() { return $this->belongsTo(Item::class); }
}
