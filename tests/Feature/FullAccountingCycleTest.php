<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Cashbox;
use App\Models\Currency;
use App\Models\FiscalYear;
use App\Models\Item;
use App\Models\Person;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Exercises the same cycle verified manually in the browser: purchase → sale
 * (with COGS) → cash receipt, and checks the books stay balanced throughout.
 */
class FullAccountingCycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_purchase_then_sale_then_receipt_keeps_the_ledger_balanced(): void
    {
        $supplier = Person::create(['name' => 'Supplier Co', 'is_supplier' => true]);
        $customer = Person::create(['name' => 'Customer Co', 'is_customer' => true]);
        $unit = Unit::first();
        $warehouse = Warehouse::first();
        $item = Item::create([
            'code' => 'ITM-1', 'name' => 'Widget', 'unit_id' => $unit->id, 'warehouse_id' => $warehouse->id,
            'cost_price' => 100, 'sale_price' => 150,
        ]);
        $currency = Currency::where('is_base', true)->first();
        $cashbox = Cashbox::first();

        // Purchase 10 units @ 100 = 1000
        $this->post(route('purchase-invoices.store'), [
            'date' => now()->toDateString(),
            'person_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'currency_id' => $currency->id,
            'fx_rate' => 1,
            'lines' => [['item_id' => $item->id, 'quantity' => 10, 'unit_price' => 100, 'discount' => 0]],
        ])->assertRedirect();

        $this->assertSame(10.0, $item->fresh()->quantityOnHand());
        $this->assertSame(1000.0, Account::where('code', '1300')->first()->balance());
        $this->assertSame(1000.0, Account::where('code', '2100')->first()->balance());

        // Sell 4 units @ 150 = 600 (cost 400)
        $this->post(route('sales-invoices.store'), [
            'date' => now()->toDateString(),
            'person_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'currency_id' => $currency->id,
            'fx_rate' => 1,
            'lines' => [['item_id' => $item->id, 'quantity' => 4, 'unit_price' => 150, 'discount' => 0]],
        ])->assertRedirect();

        $this->assertSame(6.0, $item->fresh()->quantityOnHand());
        $this->assertSame(600.0, Account::where('code', '1200')->first()->balance());
        $this->assertSame(600.0, Account::where('code', '4100')->first()->balance());
        $this->assertSame(400.0, Account::where('code', '5100')->first()->balance());
        $this->assertSame(600.0, Account::where('code', '1300')->first()->balance()); // 1000 - 400 COGS

        // Receive 250 cash from the customer against their balance
        $this->post(route('cash-vouchers.store'), [
            'type' => 'receipt',
            'date' => now()->toDateString(),
            'source' => 'cashbox',
            'cashbox_id' => $cashbox->id,
            'person_id' => $customer->id,
            'amount' => 250,
            'currency_id' => $currency->id,
            'fx_rate' => 1,
        ])->assertRedirect();

        $this->assertSame(250.0, Account::where('code', '1100')->first()->balance());
        $this->assertSame(350.0, Account::where('code', '1200')->first()->balance());

        // The books must balance across every account, every time.
        $totalDebit = 0;
        $totalCredit = 0;
        foreach (Account::where('is_group', false)->get() as $account) {
            $totalDebit += (float) $account->journalLines()->sum('base_debit');
            $totalCredit += (float) $account->journalLines()->sum('base_credit');
        }
        $this->assertEqualsWithDelta($totalDebit, $totalCredit, 0.001);
    }
}
