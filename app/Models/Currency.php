<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'symbol', 'is_base'];

    protected $casts = [
        'is_base' => 'boolean',
    ];

    public function exchangeRates()
    {
        return $this->hasMany(ExchangeRate::class);
    }

    public function latestRate(?string $onDate = null): float
    {
        if ($this->is_base) {
            return 1.0;
        }

        return (float) $this->exchangeRates()
            ->when($onDate, fn ($q) => $q->where('effective_date', '<=', $onDate))
            ->orderByDesc('effective_date')
            ->value('rate') ?? 1.0;
    }
}
