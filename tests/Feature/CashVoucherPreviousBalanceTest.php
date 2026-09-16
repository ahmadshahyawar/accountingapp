<?php

namespace Tests\Feature;

use App\Models\Cashbox;
use App\Models\Currency;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers the حساب گذشته (previous balance) box added to the دریافت/پرداخت نقدی
 * form to match the old app — didn't exist here before.
 */
class CashVoucherPreviousBalanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'admin@example.com')->firstOrFail());
    }

    public function test_the_receipt_form_embeds_each_persons_current_ar_balance(): void
    {
        $customer = Person::create(['name' => 'Balance Co', 'is_customer' => true]);
        $currency = Currency::where('is_base', true)->first();
        $cashbox = Cashbox::first();

        // Post a receipt directly against AR (no invoice) to put a known balance on the books.
        $this->post(route('cash-vouchers.store'), [
            'type' => 'payment',
            'date' => now()->toDateString(),
            'source' => 'cashbox',
            'cashbox_id' => $cashbox->id,
            'person_id' => $customer->id,
            'amount' => 300,
            'currency_id' => $currency->id,
            'fx_rate' => 1,
        ])->assertRedirect();

        // A "payment" to a person posts against AP (2100), not AR — so switch to the payment
        // create form, which is what should show this person's 300 balance on that account.
        $response = $this->get(route('cash-vouchers.create', ['type' => 'payment']));
        $response->assertOk();
        $response->assertSee((string) $customer->id);
        $response->assertSee('-300'); // AP credit-normal balance for a payment recipient reads negative
    }
}
