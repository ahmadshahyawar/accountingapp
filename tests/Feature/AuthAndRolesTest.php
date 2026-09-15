<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Currency;
use App\Models\FiscalYear;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * The app has no auth at all before this change - anyone could open any
 * screen and delete any record. Covers: login/logout, the inactive-account
 * block, every route requiring auth, only admins being able to delete
 * (RestrictDestroyToAdmin, keyed off the ".destroy" route-name convention),
 * and user management itself being admin-only.
 */
class AuthAndRolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_with_correct_credentials_succeeds(): void
    {
        $this->post(route('login.attempt'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_login_with_wrong_password_fails(): void
    {
        $this->post(route('login.attempt'), [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_a_deactivated_user_cannot_log_in(): void
    {
        $user = User::create([
            'name' => 'Deactivated', 'email' => 'off@example.com',
            'password' => Hash::make('password'), 'role' => 'user', 'is_active' => false,
        ]);

        $this->post(route('login.attempt'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_every_module_route_redirects_a_guest_to_login(): void
    {
        $this->get(route('sales-invoices.index'))->assertRedirect(route('login'));
        $this->get(route('money-transfers.index'))->assertRedirect(route('login'));
        $this->get(route('users.index'))->assertRedirect(route('login'));
    }

    public function test_logout_ends_the_session(): void
    {
        $this->actingAs(User::where('email', 'admin@example.com')->first());
        $this->assertAuthenticated();

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_a_regular_user_cannot_delete_a_record_but_can_still_use_the_module(): void
    {
        $regular = User::create([
            'name' => 'Regular', 'email' => 'regular@example.com',
            'password' => Hash::make('password'), 'role' => 'user', 'is_active' => true,
        ]);
        $this->actingAs($regular);

        $customer = Person::create(['name' => 'Test Customer', 'is_customer' => true]);
        $currency = Currency::where('is_base', true)->first();
        FiscalYear::current(); // make sure a fiscal year exists (seeded)

        // Regular users can still fully use the module — create a cash voucher...
        $expenseAccount = Account::where('type', 'expense')->where('is_group', false)->first();
        $this->post(route('cash-vouchers.store'), [
            'type' => 'payment', 'date' => now()->toDateString(), 'source' => 'cashbox',
            'cashbox_id' => \App\Models\Cashbox::first()->id, 'contra_account_id' => $expenseAccount->id,
            'amount' => 100, 'currency_id' => $currency->id, 'fx_rate' => 1,
        ])->assertRedirect(route('cash-vouchers.index'));
        $voucher = \App\Models\CashVoucher::first();

        // ...but deleting it is refused.
        $this->delete(route('cash-vouchers.destroy', $voucher))->assertForbidden();
        $this->assertDatabaseHas('cash_vouchers', ['id' => $voucher->id]);
    }

    public function test_an_admin_can_delete_a_record(): void
    {
        $this->actingAs(User::where('email', 'admin@example.com')->first());

        $currency = Currency::where('is_base', true)->first();
        $expenseAccount = Account::where('type', 'expense')->where('is_group', false)->first();
        $this->post(route('cash-vouchers.store'), [
            'type' => 'payment', 'date' => now()->toDateString(), 'source' => 'cashbox',
            'cashbox_id' => \App\Models\Cashbox::first()->id, 'contra_account_id' => $expenseAccount->id,
            'amount' => 100, 'currency_id' => $currency->id, 'fx_rate' => 1,
        ]);
        $voucher = \App\Models\CashVoucher::first();

        $this->delete(route('cash-vouchers.destroy', $voucher))->assertRedirect(route('cash-vouchers.index'));
        $this->assertDatabaseMissing('cash_vouchers', ['id' => $voucher->id]);
    }

    public function test_only_an_admin_can_reach_user_management(): void
    {
        $regular = User::create([
            'name' => 'Regular', 'email' => 'regular2@example.com',
            'password' => Hash::make('password'), 'role' => 'user', 'is_active' => true,
        ]);
        $this->actingAs($regular);

        $this->get(route('users.index'))->assertForbidden();
    }

    public function test_an_admin_can_create_and_delete_another_user(): void
    {
        $this->actingAs(User::where('email', 'admin@example.com')->first());

        $this->post(route('users.store'), [
            'name' => 'New Person', 'email' => 'new@example.com',
            'password' => 'password123', 'role' => 'user',
        ])->assertRedirect(route('users.index'));

        $created = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertFalse($created->isAdmin());

        $this->delete(route('users.destroy', $created))->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $created->id]);
    }

    public function test_an_admin_cannot_delete_their_own_account(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $this->actingAs($admin);

        $this->delete(route('users.destroy', $admin))->assertStatus(422);
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
