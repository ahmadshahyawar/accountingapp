<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Item;
use App\Models\Person;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers کاردکس (item stock ledger) — one of the old app screens explicitly
 * called out as not yet built. Built directly from the StockMove rows every
 * other module already writes, so no new data source was needed.
 */
class KardexTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'admin@example.com')->firstOrFail());
    }

    public function test_kardex_shows_a_running_balance_across_a_purchase_and_a_sale(): void
    {
        $unit = Unit::first();
        $warehouse = Warehouse::first();
        $currency = Currency::where('is_base', true)->first();
        $supplier = Person::create(['name' => 'Kardex Supplier', 'is_supplier' => true]);
        $customer = Person::create(['name' => 'Kardex Customer', 'is_customer' => true]);
        $item = Item::create(['code' => 'ITM-KX', 'name' => 'Kardex Widget', 'unit_id' => $unit->id, 'cost_price' => 10, 'sale_price' => 20]);

        $this->post(route('purchase-invoices.store'), [
            'date' => now()->toDateString(), 'person_id' => $supplier->id, 'warehouse_id' => $warehouse->id,
            'currency_id' => $currency->id, 'fx_rate' => 1,
            'lines' => [['item_id' => $item->id, 'quantity' => 20, 'unit_price' => 10, 'discount' => 0]],
        ])->assertRedirect();

        $this->post(route('sales-invoices.store'), [
            'date' => now()->toDateString(), 'person_id' => $customer->id, 'warehouse_id' => $warehouse->id,
            'currency_id' => $currency->id, 'fx_rate' => 1,
            'lines' => [['item_id' => $item->id, 'quantity' => 6, 'unit_price' => 20, 'discount' => 0]],
        ])->assertRedirect();

        $response = $this->get(route('reports.kardex', ['item_id' => $item->id]));

        $response->assertOk();
        $response->assertSeeInOrder(['20.00', '14.00']); // balance after purchase, then after sale
        $response->assertSee('Kardex Supplier'); // خریدار/فروشنده column
        $response->assertSee('Kardex Customer');
    }

    public function test_kardex_without_an_item_selected_shows_a_prompt_not_an_error(): void
    {
        $this->get(route('reports.kardex'))->assertOk()->assertSee('یک جنس را برای نمایش کاردکس انتخاب کنید');
    }
}
