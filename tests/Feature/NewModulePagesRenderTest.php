<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Blade compile/render errors on GET pages aren't caught by the POST-focused
 * NewModulesTest, since that never loads the form/index views — this does.
 */
class NewModulePagesRenderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_every_new_module_index_and_create_page_renders(): void
    {
        $routes = [
            'money-transfers.index', 'money-transfers.create',
            'account-transfers.index', 'account-transfers.create',
            'currency-exchanges.index', 'currency-exchanges.create',
            'item-transfers.index', 'item-transfers.create',
            'proforma-invoices.index', 'proforma-invoices.create',
            'sales-returns.index', 'sales-returns.create',
            'purchase-returns.index', 'purchase-returns.create',
        ];

        foreach ($routes as $name) {
            $this->get(route($name))->assertOk();
        }
    }
}
