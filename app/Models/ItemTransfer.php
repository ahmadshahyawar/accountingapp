<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTransfer extends Model
{
    use HasFactory;

    protected $fillable = ['number', 'date', 'from_warehouse_id', 'to_warehouse_id', 'notes'];

    protected $casts = ['date' => 'date'];

    public function fromWarehouse() { return $this->belongsTo(Warehouse::class, 'from_warehouse_id'); }
    public function toWarehouse() { return $this->belongsTo(Warehouse::class, 'to_warehouse_id'); }
    public function lines() { return $this->hasMany(ItemTransferLine::class); }
}
