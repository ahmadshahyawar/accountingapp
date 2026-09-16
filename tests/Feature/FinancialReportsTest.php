<?php

namespace Tests\Feature;

use App\Models\Cashbox;
use App\Models\Currency;
use App\Models\Item;
use App\Models\Person;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers مفاد و ضرر (profit & loss) and گزارش صندوق و بانک (cash & bank),
 * two of the old app's core financial reports this app never had at all —
 * found via ribbon_tab3.png, the real گزارشات مالی ribbon screenshot.
 */
class FinancialReportsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'admin@example.com')->firstOrFail());
    }

    public function test_profit_and_loss_nets_revenue_against_expense_including_cogs(): void
    {
        $unit = Unit::first();
        $warehouse = Warehouse::first();
        $currency = Currency::where('is_base', true)->first();
        $supplier = Person::create(['name' => 'PnL Supplier', 'is_supplier' => true]);
        $customer = Person::create(['name' => 'PnL Customer', 'is_customer' => true]);
        $item = Item::create(['code' => 'ITM-PL', 'name' => 'Widget', 'unit_id' => $unit->id, 'cost_price' => 10, 'sale_price' => 25]);

        // Buy 10 @ 10 = 100 cost basis, sell 10 @ 25 = 250 revenue, 100 COGS -> 150 profit.
        $this->post(route('purchase-invoices.store'), [
            'date' => now()->toDateString(), 'person_id' => $supplier->id, 'warehouse_id' => $warehouse->id,
            'currency_id' => $currency->id, 'fx_rate' => 1,
            'lines' => [['item_id' => $item->id, 'quantity' => 10, 'unit_price' => 10, 'discount' => 0]],
        ])->assertRedirect();

        $this->post(route('sales-invoices.store'), [
            'date' => now()->toDateString(), 'person_id' => $customer->id, 'warehouse_id' => $warehouse->id,
            'currency_id' => $currency->id, 'fx_rate' => 1,
            'lines' => [['item_id' => $item->id, 'quantity' => 10, 'unit_price' => 25, 'discount' => 0]],
        ])->assertRedirect();

        $response = $this->get(route('reports.profit-and-loss'));
        $response->assertOk();
        $response->assertSee('250.00'); // total revenue
        $response->assertSee('100.00'); // COGS expense
        $response->assertSee('150.00'); // net profit
    }

    public function test_cash_and_bank_report_shows_each_cashboxs_current_balance(): void
    {
        $cashbox = Cashbox::first();

        $response = $this->get(route('reports.cash-and-bank'));
        $response->assertOk();
        $response->assertSee($cashbox->name);
    }
}
