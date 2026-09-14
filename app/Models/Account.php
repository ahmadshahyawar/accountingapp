<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'parent_id', 'type', 'normal_balance', 'is_group', 'is_active',
    ];

    protected $casts = [
        'is_group' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalLines()
    {
        return $this->hasMany(JournalLine::class);
    }

    /**
     * Current balance in base currency, signed to the account's normal balance
     * (positive = normal balance side, e.g. a positive asset balance is a debit balance).
     */
    public function balance(): float
    {
        $debit = (float) $this->journalLines()->sum('base_debit');
        $credit = (float) $this->journalLines()->sum('base_credit');

        return $this->normal_balance === 'debit' ? $debit - $credit : $credit - $debit;
    }
}
