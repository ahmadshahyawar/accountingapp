<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\Cashbox;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Person;
use App\Models\PurchaseInvoice;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the 7 modules discovered from the old app's UI during manual exploration
 * that weren't in the first build: money transfer, account transfer, currency
 * exchange, item transfer, proforma invoice, and sales/purchase returns.
 */
class NewModulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_money_transfer_moves_balance_between_cashbox_and_bank_and_stays_balanced(): void
    {
        $cashbox = Cashbox::first();
        $afn = Currency::where('is_base', true)->first();
        $bankAccountLedger = Account::create([
            'code' => '1150-1', 'name' => 'بانک ملی', 'type' => 'asset', 'normal_balance' => 'debit', 'parent_id' => Account::where('code', '1150')->value('id'),
        ]);
        $bank = BankAccount::create(['name' => 'بانک ملی', 'currency_id' => $afn->id, 'account_id' => $bankAccountLedger->id]);

        $this->post(route('money-transfers.store'), [
            'date' => now()->toDateString(),
            'from_type' => 'cashbox',
            'from_cashbox_id' => $cashbox->id,
            'to_type' => 'bank',
            'to_bank_account_id' => $bank->id,
            'amount' => 5000,
            'currency_id' => $afn->id,
            'fx_rate' => 1,
        ])->assertRedirect(route('money-transfers.index'));

        $this->assertSame(-5000.0, Account::where('code', '1100')->first()->balance());
        $this->assertSame(5000.0, $bankAccountLedger->fresh()->balance());
        $this->assertDatabaseCount('money_transfers', 1);
    }

    public function test_account_transfer_moves_balance_between_two_persons_and_stays_balanced(): void
    {
        $ar = Account::where('code', '1200')->first();
        $a = Person::create(['name' => 'Person A', 'is_customer' => true]);
        $b = Person::create(['name' => 'Person B', 'is_customer' => true]);
        $afn = Currency::where('is_base', true)->first();

        $this->post(route('account-transfers.store'), [
            'date' => now()->toDateString(),
            'from_account_id' => $ar->id,
            'from_person_id' => $a->id,
            'to_account_id' => $ar->id,
            'to_person_id' => $b->id,
            'amount' => 1200,
            'currency_id' => $afn->id,
            'fx_rate' => 1,
        ])->assertRedirect(route('account-transfers.index'));

        $aBalance = (float) $ar->journalLines()->where('person_id', $a->id)->selectRaw('sum(base_debit) - sum(base_credit) as b')->value('b');
        $bBalance = (float) $ar->journalLines()->where('person_id', $b->id)->selectRaw('sum(base_debit) - sum(base_credit) as b')->value('b');

        $this->assertSame(1200.0, $aBalance);
        $this->assertSame(-1200.0, $bBalance);
    }

    public function test_currency_exchange_is_recorded_with_computed_gain_loss(): void
    {
        $cashbox = Cashbox::first();
        $afn = Currency::where('is_base', true)->first();
        $usd = Currency::where('code', 'USD')->first();

        $this->post(route('currency-exchanges.store'), [
            'date' => now()->toDateString(),
            'holder_type' => 'cashbox',
            'cashbox_id' => $cashbox->id,
            'paid_currency_id' => $afn->id,
            'paid_amount' => 7000,
            'paid_rate' => 1,
            'received_currency_id' => $usd->id,
            'received_amount' => 100,
            'received_rate' => 70,
            'description' => 'test exchange',
        ])->assertRedirect(route('currency-exchanges.index'));

        $exchange = \App\Models\CurrencyExchange::first();
        $this->assertSame(0.0, $exchange->gainLoss());
    }

    public function test_item_transfer_moves_stock_between_warehouses(): void
    {
        $unit = Unit::first();
        $wh1 = Warehouse::first();
        $wh2 = Warehouse::create(['name' => 'گدام دوم']);
        $item = Item::create([
            'code' => 'ITM-2', 'name' => 'Gadget', 'unit_id' => $unit->id, 'warehouse_id' => $wh1->id,
            'cost_price' => 50, 'sale_price' => 80,
        ]);

        // Give warehouse 1 some stock first via an opening-style stock move through a purchase.
        $supplier = Person::create(['name' => 'Supplier X', 'is_supplier' => true]);
        $currency = Currency::where('is_base', true)->first();
        $this->post(route('purchase-invoices.store'), [
            'date' => now()->toDateString(),
            'person_id' => $supplier->id,
            'warehouse_id' => $wh1->id,
            'currency_id' => $currency->id,
            'fx_rate' => 1,
            'lines' => [['item_id' => $item->id, 'quantity' => 20, 'unit_price' => 50, 'discount' => 0]],
        ])->assertRedirect();

        $this->post(route('item-transfers.store'), [
            'date' => now()->toDateString(),
            'from_warehouse_id' => $wh1->id,
            'to_warehouse_id' => $wh2->id,
            'lines' => [['item_id' => $item->id, 'quantity' => 8]],
        ])->assertRedirect(route('item-transfers.index'));

        $this->assertSame(12.0, $item->fresh()->quantityOnHand($wh1->id));
        $this->assertSame(8.0, $item->fresh()->quantityOnHand($wh2->id));
        $this->assertSame(20.0, $item->fresh()->quantityOnHand()); // total unchanged
    }

    public function test_proforma_invoice_records_a_quote_without_touching_stock_or_ledger(): void
    {
        $unit = Unit::first();
        $wh = Warehouse::first();
        $item = Item::create([
            'code' => 'ITM-3', 'name' => 'Quoted Thing', 'unit_id' => $unit->id, 'warehouse_id' => $wh->id,
            'cost_price' => 10, 'sale_price' => 20,
        ]);
        $currency = Currency::where('is_base', true)->first();

        $this->post(route('proforma-invoices.store'), [
            'date' => now()->toDateString(),
            'customer_name' => 'Walk-in Prospect',
            'customer_phone' => '0700000000',
            'warehouse_id' => $wh->id,
            'currency_id' => $currency->id,
            'fx_rate' => 1,
            'discount' => 0,
            'lines' => [['item_id' => $item->id, 'quantity' => 3, 'unit_price' => 20]],
        ])->assertRedirect(route('proforma-invoices.index'));

        $this->assertSame(0.0, $item->fresh()->quantityOnHand()); // no stock movement — it's just a quote
        $this->assertSame(0, \App\Models\JournalEntry::count()); // no ledger impact
        $this->assertDatabaseHas('proforma_invoices', ['customer_name' => 'Walk-in Prospect', 'total_amount' => 60]);
    }

    public function test_purchase_return_reverses_stock_and_payable(): void
    {
        $unit = Unit::first();
        $wh = Warehouse::first();
        $item = Item::create([
            'code' => 'ITM-4', 'name' => 'Returnable', 'unit_id' => $unit->id, 'warehouse_id' => $wh->id,
            'cost_price' => 100, 'sale_price' => 150,
        ]);
        $supplier = Person::create(['name' => 'Supplier R', 'is_supplier' => true]);
        $currency = Currency::where('is_base', true)->first();

        $this->post(route('purchase-invoices.store'), [
            'date' => now()->toDateString(),
            'person_id' => $supplier->id,
            'warehouse_id' => $wh->id,
            'currency_id' => $currency->id,
            'fx_rate' => 1,
            'lines' => [['item_id' => $item->id, 'quantity' => 10, 'unit_price' => 100, 'discount' => 0]],
        ])->assertRedirect();

        $this->assertSame(1000.0, Account::where('code', '2100')->first()->balance());

        $this->post(route('purchase-returns.store'), [
            'date' => now()->toDateString(),
            'person_id' => $supplier->id,
            'warehouse_id' => $wh->id,
            'currency_id' => $currency->id,
            'fx_rate' => 1,
            'lines' => [['item_id' => $item->id, 'quantity' => 2, 'unit_price' => 100]],
        ])->assertRedirect(route('purchase-returns.index'));

        $this->assertSame(8.0, $item->fresh()->quantityOnHand());
        $this->assertSame(800.0, Account::where('code', '2100')->first()->balance());
        $this->assertSame(800.0, Account::where('code', '1300')->first()->balance());
    }

    public function test_sales_return_reverses_stock_revenue_and_cogs(): void
    {
        $unit = Unit::first();
        $wh = Warehouse::first();
        $item = Item::create([
            'code' => 'ITM-5', 'name' => 'Sold Thing', 'unit_id' => $unit->id, 'warehouse_id' => $wh->id,
            'cost_price' => 100, 'sale_price' => 150,
        ]);
        $supplier = Person::create(['name' => 'Supplier S', 'is_supplier' => true]);
        $customer = Person::create(['name' => 'Customer S', 'is_customer' => true]);
        $currency = Currency::where('is_base', true)->first();

        $this->post(route('purchase-invoices.store'), [
            'date' => now()->toDateString(),
            'person_id' => $supplier->id,
            'warehouse_id' => $wh->id,
            'currency_id' => $currency->id,
            'fx_rate' => 1,
            'lines' => [['item_id' => $item->id, 'quantity' => 10, 'unit_price' => 100, 'discount' => 0]],
        ])->assertRedirect();

        $this->post(route('sales-invoices.store'), [
            'date' => now()->toDateString(),
            'person_id' => $customer->id,
            'warehouse_id' => $wh->id,
            'currency_id' => $currency->id,
            'fx_rate' => 1,
            'lines' => [['item_id' => $item->id, 'quantity' => 4, 'unit_price' => 150, 'discount' => 0]],
        ])->assertRedirect();

        $this->assertSame(600.0, Account::where('code', '1200')->first()->balance());
        $this->assertSame(400.0, Account::where('code', '5100')->first()->balance());

        $this->post(route('sales-returns.store'), [
            'date' => now()->toDateString(),
            'person_id' => $customer->id,
            'warehouse_id' => $wh->id,
            'currency_id' => $currency->id,
            'fx_rate' => 1,
            'lines' => [['item_id' => $item->id, 'quantity' => 1, 'unit_price' => 150]],
        ])->assertRedirect(route('sales-returns.index'));

        $this->assertSame(7.0, $item->fresh()->quantityOnHand()); // 10 - 4 + 1
        $this->assertSame(450.0, Account::where('code', '1200')->first()->balance()); // 600 - 150
        $this->assertSame(300.0, Account::where('code', '5100')->first()->balance()); // 400 - 100
    }

    /** Every new module's postings must keep the books balanced, same guarantee as the original cycle test. */
    public function test_all_new_modules_together_keep_the_ledger_balanced(): void
    {
        $unit = Unit::first();
        $wh1 = Warehouse::first();
        $wh2 = Warehouse::create(['name' => 'گدام دوم']);
        $afn = Currency::where('is_base', true)->first();
        $usd = Currency::where('code', 'USD')->first();
        $cashbox = Cashbox::first();
        $bankLedger = Account::create([
            'code' => '1150-1', 'name' => 'بانک ملی', 'type' => 'asset', 'normal_balance' => 'debit', 'parent_id' => Account::where('code', '1150')->value('id'),
        ]);
        $bank = BankAccount::create(['name' => 'بانک ملی', 'currency_id' => $afn->id, 'account_id' => $bankLedger->id]);
        $supplier = Person::create(['name' => 'Supplier All', 'is_supplier' => true]);
        $customer = Person::create(['name' => 'Customer All', 'is_customer' => true]);
        $item = Item::create([
            'code' => 'ITM-6', 'name' => 'AllModulesItem', 'unit_id' => $unit->id, 'warehouse_id' => $wh1->id,
            'cost_price' => 100, 'sale_price' => 150,
        ]);

        $this->post(route('purchase-invoices.store'), ['date' => now()->toDateString(), 'person_id' => $supplier->id, 'warehouse_id' => $wh1->id, 'currency_id' => $afn->id, 'fx_rate' => 1, 'lines' => [['item_id' => $item->id, 'quantity' => 20, 'unit_price' => 100, 'discount' => 0]]]);
        $this->post(route('sales-invoices.store'), ['date' => now()->toDateString(), 'person_id' => $customer->id, 'warehouse_id' => $wh1->id, 'currency_id' => $afn->id, 'fx_rate' => 1, 'lines' => [['item_id' => $item->id, 'quantity' => 5, 'unit_price' => 150, 'discount' => 0]]]);
        $this->post(route('sales-returns.store'), ['date' => now()->toDateString(), 'person_id' => $customer->id, 'warehouse_id' => $wh1->id, 'currency_id' => $afn->id, 'fx_rate' => 1, 'lines' => [['item_id' => $item->id, 'quantity' => 1, 'unit_price' => 150]]]);
        $this->post(route('purchase-returns.store'), ['date' => now()->toDateString(), 'person_id' => $supplier->id, 'warehouse_id' => $wh1->id, 'currency_id' => $afn->id, 'fx_rate' => 1, 'lines' => [['item_id' => $item->id, 'quantity' => 2, 'unit_price' => 100]]]);
        $this->post(route('money-transfers.store'), ['date' => now()->toDateString(), 'from_type' => 'cashbox', 'from_cashbox_id' => $cashbox->id, 'to_type' => 'bank', 'to_bank_account_id' => $bank->id, 'amount' => 1000, 'currency_id' => $afn->id, 'fx_rate' => 1]);
        $this->post(route('account-transfers.store'), ['date' => now()->toDateString(), 'from_account_id' => Account::where('code', '1200')->value('id'), 'from_person_id' => $customer->id, 'to_account_id' => Account::where('code', '1200')->value('id'), 'to_person_id' => null, 'amount' => 100, 'currency_id' => $afn->id, 'fx_rate' => 1]);
        $this->post(route('item-transfers.store'), ['date' => now()->toDateString(), 'from_warehouse_id' => $wh1->id, 'to_warehouse_id' => $wh2->id, 'lines' => [['item_id' => $item->id, 'quantity' => 3]]]);
        $this->post(route('currency-exchanges.store'), ['date' => now()->toDateString(), 'holder_type' => 'cashbox', 'cashbox_id' => $cashbox->id, 'paid_currency_id' => $afn->id, 'paid_amount' => 700, 'paid_rate' => 1, 'received_currency_id' => $usd->id, 'received_amount' => 10, 'received_rate' => 70]);

        $totalDebit = 0;
        $totalCredit = 0;
        foreach (Account::where('is_group', false)->get() as $account) {
            $totalDebit += (float) $account->journalLines()->sum('base_debit');
            $totalCredit += (float) $account->journalLines()->sum('base_credit');
        }
        $this->assertEqualsWithDelta($totalDebit, $totalCredit, 0.001);
    }
}
