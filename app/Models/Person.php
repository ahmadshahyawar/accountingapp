<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    // Eloquent's default pluralization would guess "people" — the migration uses "persons".
    protected $table = 'persons';

    protected $fillable = [
        'name', 'phone', 'address', 'is_customer', 'is_supplier', 'is_employee', 'notes',
    ];

    protected $casts = [
        'is_customer' => 'boolean',
        'is_supplier' => 'boolean',
        'is_employee' => 'boolean',
    ];

    public function scopeCustomers($query)
    {
        return $query->where('is_customer', true);
    }

    public function scopeSuppliers($query)
    {
        return $query->where('is_supplier', true);
    }

    public function scopeEmployees($query)
    {
        return $query->where('is_employee', true);
    }

    public function journalLines()
    {
        return $this->hasMany(JournalLine::class);
    }
}
