<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturnLine extends Model
{
    use HasFactory;

    protected $fillable = ['sales_return_id', 'item_id', 'quantity', 'unit_price', 'total'];

    public function salesReturn() { return $this->belongsTo(SalesReturn::class); }
    public function item() { return $this->belongsTo(Item::class); }
}
