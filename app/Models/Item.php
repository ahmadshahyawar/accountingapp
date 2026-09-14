<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'unit_id', 'warehouse_id', 'cost_price', 'sale_price', 'reorder_level', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function stockMoves()
    {
        return $this->hasMany(StockMove::class);
    }

    public function quantityOnHand(?int $warehouseId = null): float
    {
        $in = $this->stockMoves()->where('type', 'in')
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->sum('quantity');

        $out = $this->stockMoves()->where('type', 'out')
            ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
            ->sum('quantity');

        return (float) $in - (float) $out;
    }
}
