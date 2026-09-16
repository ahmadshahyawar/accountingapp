<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Cashbox;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Person;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the تخفیف/مصارف/دریافت-پرداخت fields added to match the old app's
 * فاکتور فروش/خرید forms — these didn't exist on the invoice at all before.
 */
class InvoiceDiscountExpensePaidTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'admin@example.com')->firstOrFail());
    }

    private function baseData(): array
    {
        return [
            'unit' => Unit::first(),
            'warehouse' => Warehouse::first(),
            'currency' => Currency::where('is_base', true)->first(),
            'cashbox' => Cashbox::first(),
        ];
    }

    public function test_sales_invoice_folds_discount_and_expense_into_the_total_and_posts_cash_received(): void
    {
        $base = $this->baseData();
        $customer = Person::create(['name' => 'Discount Co', 'is_customer' => true]);
        $item = Item::create(['code' => 'ITM-D1', 'name' => 'Widget', 'unit_id' => $base['unit']->id, 'cost_price' => 10, 'sale_price' => 50]);

        // 5 x 50 = 250, minus 20 discount, plus 10 expense = 240 total; 100 received now.
        $this->post(route('sales-invoices.store'), [
            'date' => now()->toDateString(),
            'person_id' => $customer->id,
            'warehouse_id' => $base['warehouse']->id,
            'currency_id' => $base['currency']->id,
            'fx_rate' => 1,
            'discount' => 20,
            'expense' => 10,
            'paid_amount' => 100,
            'lines' => [['item_id' => $item->id, 'quantity' => 5, 'unit_price' => 50, 'discount' => 0]],
        ])->assertRedirect();

        $invoice = SalesInvoice::firstOrFail();
        $this->assertSame(240.0, (float) $invoice->total_amount);
        $this->assertSame(20.0, (float) $invoice->discount);
        $this->assertSame(10.0, (float) $invoice->expense);
        $this->assertSame(100.0, (float) $invoice->paid_amount);

        // AR should carry only the unpaid remainder (240 owed - 100 received).
        $this->assertSame(140.0, Account::where('code', '1200')->first()->balance());
        $this->assertSame(100.0, $base['cashbox']->account->fresh()->balance());
    }

    public function test_purchase_invoice_folds_discount_and_expense_into_the_total_and_posts_cash_paid(): void
    {
        $base = $this->baseData();
        $supplier = Person::create(['name' => 'Discount Supplier', 'is_supplier' => true]);
        $item = Item::create(['code' => 'ITM-D2', 'name' => 'Widget', 'unit_id' => $base['unit']->id, 'cost_price' => 10, 'sale_price' => 50]);

        $this->post(route('purchase-invoices.store'), [
            'date' => now()->toDateString(),
            'person_id' => $supplier->id,
            'warehouse_id' => $base['warehouse']->id,
            'currency_id' => $base['currency']->id,
            'fx_rate' => 1,
            'discount' => 15,
            'expense' => 5,
            'paid_amount' => 50,
            'lines' => [['item_id' => $item->id, 'quantity' => 5, 'unit_price' => 20, 'discount' => 0]],
        ])->assertRedirect();

        $invoice = PurchaseInvoice::firstOrFail();
        // 5 x 20 = 100, minus 15 discount, plus 5 expense = 90 total.
        $this->assertSame(90.0, (float) $invoice->total_amount);

        // AP should carry only the unpaid remainder (90 owed - 50 paid).
        $this->assertSame(40.0, Account::where('code', '2100')->first()->balance());
        $this->assertSame(-50.0, $base['cashbox']->account->fresh()->balance());
    }

    public function test_the_create_forms_show_the_customers_previous_balance(): void
    {
        $base = $this->baseData();
        $customer = Person::create(['name' => 'Returning Customer', 'is_customer' => true]);
        $item = Item::create(['code' => 'ITM-D3', 'name' => 'Widget', 'unit_id' => $base['unit']->id, 'cost_price' => 10, 'sale_price' => 50]);

        $this->post(route('sales-invoices.store'), [
            'date' => now()->toDateString(),
            'person_id' => $customer->id,
            'warehouse_id' => $base['warehouse']->id,
            'currency_id' => $base['currency']->id,
            'fx_rate' => 1,
            'lines' => [['item_id' => $item->id, 'quantity' => 2, 'unit_price' => 50, 'discount' => 0]],
        ])->assertRedirect();

        // The create page embeds a person_id => balance map for the حساب قبلی box.
        $response = $this->get(route('sales-invoices.create'));
        $response->assertOk();
        $response->assertSee((string) $customer->id);
        $response->assertSee('100'); // that customer's fresh AR balance
    }
}
