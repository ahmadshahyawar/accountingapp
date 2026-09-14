<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Currency;
use App\Models\FiscalYear;
use App\Services\LedgerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class LedgerServiceTest extends TestCase
{
    use RefreshDatabase;

    private Currency $base;

    private FiscalYear $year;

    private Account $cash;

    private Account $revenue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->base = Currency::create(['code' => 'AFN', 'name' => 'Afghani', 'is_base' => true]);
        $this->year = FiscalYear::create([
            'name' => '1405', 'start_date' => '2026-01-01', 'end_date' => '2026-12-29', 'is_current' => true,
        ]);
        $this->cash = Account::create(['code' => '1000', 'name' => 'Cash', 'type' => 'asset', 'normal_balance' => 'debit']);
        $this->revenue = Account::create(['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'revenue', 'normal_balance' => 'credit']);
    }

    public function test_a_balanced_entry_posts_and_updates_account_balances(): void
    {
        $entry = app(LedgerService::class)->postEntry([
            'fiscal_year_id' => $this->year->id,
            'date' => '2026-01-05',
            'reference_type' => 'manual',
            'reference_id' => null,
            'description' => 'Cash sale',
            'currency_id' => $this->base->id,
            'fx_rate' => 1,
            'lines' => [
                ['account_id' => $this->cash->id, 'debit' => 1000, 'credit' => 0],
                ['account_id' => $this->revenue->id, 'debit' => 0, 'credit' => 1000],
            ],
        ]);

        $this->assertCount(2, $entry->lines);
        $this->assertSame(1000.0, $this->cash->fresh()->balance());
        $this->assertSame(1000.0, $this->revenue->fresh()->balance());
    }

    public function test_an_unbalanced_entry_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(LedgerService::class)->postEntry([
            'fiscal_year_id' => $this->year->id,
            'date' => '2026-01-05',
            'reference_type' => 'manual',
            'reference_id' => null,
            'description' => 'Broken entry',
            'currency_id' => $this->base->id,
            'lines' => [
                ['account_id' => $this->cash->id, 'debit' => 1000, 'credit' => 0],
                ['account_id' => $this->revenue->id, 'debit' => 0, 'credit' => 900],
            ],
        ]);
    }

    public function test_reversing_an_entry_flips_debits_and_credits(): void
    {
        $ledger = app(LedgerService::class);

        $entry = $ledger->postEntry([
            'fiscal_year_id' => $this->year->id,
            'date' => '2026-01-05',
            'reference_type' => 'manual',
            'reference_id' => null,
            'description' => 'Original',
            'currency_id' => $this->base->id,
            'lines' => [
                ['account_id' => $this->cash->id, 'debit' => 500, 'credit' => 0],
                ['account_id' => $this->revenue->id, 'debit' => 0, 'credit' => 500],
            ],
        ]);

        $ledger->reverseEntry($entry, '2026-01-06');

        $this->assertSame(0.0, $this->cash->fresh()->balance());
        $this->assertSame(0.0, $this->revenue->fresh()->balance());
    }
}
