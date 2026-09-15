<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Cashbox;
use App\Models\Currency;
use App\Models\FiscalYear;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Morilog\Jalali\Jalalian;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the reference/foundation data every fresh install needs before any
     * module (sales, purchases, cash vouchers) can be used. See plan milestone 2.
     */
    public function run(): void
    {
        // Default admin — the app requires sign-in, so a fresh install needs
        // at least one account able to log in and manage other users.
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'مدیر سیستم', 'password' => Hash::make('password'), 'role' => 'admin', 'is_active' => true]
        );

        $afn = Currency::firstOrCreate(
            ['code' => 'AFN'],
            ['name' => 'افغانی', 'symbol' => '؋', 'is_base' => true]
        );
        Currency::firstOrCreate(
            ['code' => 'EUR'],
            ['name' => 'یورو', 'symbol' => '€', 'is_base' => false]
        );
        Currency::firstOrCreate(
            ['code' => 'USD'],
            ['name' => 'دالر امریکایی', 'symbol' => '$', 'is_base' => false]
        );

        $currentShamsiYear = (int) Jalalian::now()->getYear();
        $start = Jalalian::fromFormat('Y-m-d', "{$currentShamsiYear}-01-01")->toCarbon();
        $end = (clone $start)->addYear()->subDay();

        $fiscalYear = FiscalYear::firstOrCreate(
            ['name' => (string) $currentShamsiYear],
            ['start_date' => $start, 'end_date' => $end, 'is_current' => true]
        );

        Unit::firstOrCreate(['name' => 'عدد'], ['symbol' => 'عدد']);
        Unit::firstOrCreate(['name' => 'کیلوگرام'], ['symbol' => 'کیلو']);

        Warehouse::firstOrCreate(['name' => 'انبار مرکزی'], ['is_default' => true]);

        // Minimal standard chart of accounts — group headers plus the leaf accounts
        // the core modules (cash vouchers, sales, purchases) post against directly.
        $chart = [
            ['code' => '1000', 'name' => 'دارایی ها', 'type' => 'asset', 'normal_balance' => 'debit', 'is_group' => true],
            ['code' => '1100', 'name' => 'صندوق', 'type' => 'asset', 'normal_balance' => 'debit', 'parent' => '1000'],
            ['code' => '1150', 'name' => 'بانک', 'type' => 'asset', 'normal_balance' => 'debit', 'parent' => '1000'],
            ['code' => '1200', 'name' => 'حساب های دریافتنی (طلبکاران)', 'type' => 'asset', 'normal_balance' => 'debit', 'parent' => '1000'],
            ['code' => '1300', 'name' => 'موجودی اجناس', 'type' => 'asset', 'normal_balance' => 'debit', 'parent' => '1000'],

            ['code' => '2000', 'name' => 'تعهدات', 'type' => 'liability', 'normal_balance' => 'credit', 'is_group' => true],
            ['code' => '2100', 'name' => 'حساب های پرداختنی (قرضداران)', 'type' => 'liability', 'normal_balance' => 'credit', 'parent' => '2000'],

            ['code' => '3000', 'name' => 'سرمایه', 'type' => 'equity', 'normal_balance' => 'credit'],

            ['code' => '4000', 'name' => 'عواید', 'type' => 'revenue', 'normal_balance' => 'credit', 'is_group' => true],
            ['code' => '4100', 'name' => 'عواید فروش', 'type' => 'revenue', 'normal_balance' => 'credit', 'parent' => '4000'],

            ['code' => '5000', 'name' => 'مصارف', 'type' => 'expense', 'normal_balance' => 'debit', 'is_group' => true],
            ['code' => '5100', 'name' => 'بهای تمام شده اجناس فروخته شده', 'type' => 'expense', 'normal_balance' => 'debit', 'parent' => '5000'],
            ['code' => '5200', 'name' => 'مصارف عمومی', 'type' => 'expense', 'normal_balance' => 'debit', 'parent' => '5000'],
        ];

        $byCode = [];
        foreach ($chart as $row) {
            $parentCode = $row['parent'] ?? null;
            unset($row['parent']);
            $row['is_group'] = $row['is_group'] ?? false;
            $row['parent_id'] = $parentCode ? ($byCode[$parentCode]->id ?? null) : null;

            $byCode[$row['code']] = Account::firstOrCreate(['code' => $row['code']], $row);
        }

        Cashbox::firstOrCreate(
            ['name' => 'صندوق نقدی اصلی'],
            ['currency_id' => $afn->id, 'account_id' => $byCode['1100']->id]
        );
    }
}
