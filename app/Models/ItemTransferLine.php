<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTransferLine extends Model
{
    use HasFactory;

    protected $fillable = ['item_transfer_id', 'item_id', 'quantity'];

    public function itemTransfer() { return $this->belongsTo(ItemTransfer::class); }
    public function item() { return $this->belongsTo(Item::class); }
}
