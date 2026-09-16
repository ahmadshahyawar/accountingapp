<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = ['name', 'phone', 'mobile', 'email', 'website', 'address'];

    /** Always exactly one row — creates it on first access rather than requiring a seeder. */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}
