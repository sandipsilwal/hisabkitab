<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountDefaultTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function only_one_account_can_be_default_cash_account_on_creation(): void
    {
        // 1. Create a default cash account
        $account1 = Account::create([
            'name' => 'Account 1',
            'balance' => 1000,
            'is_default_cash_account' => true,
            'is_default_online_account' => false,
        ]);

        $this->assertTrue((bool) $account1->fresh()->is_default_cash_account);

        // 2. Create another default cash account
        $account2 = Account::create([
            'name' => 'Account 2',
            'balance' => 2000,
            'is_default_cash_account' => true,
            'is_default_online_account' => false,
        ]);

        // Account 1 should now be false, and Account 2 should be true
        $this->assertFalse((bool) $account1->fresh()->is_default_cash_account);
        $this->assertTrue((bool) $account2->fresh()->is_default_cash_account);
    }

    /** @test */
    public function only_one_account_can_be_default_online_account_on_creation(): void
    {
        // 1. Create a default online account
        $account1 = Account::create([
            'name' => 'Account 1',
            'balance' => 1000,
            'is_default_cash_account' => false,
            'is_default_online_account' => true,
        ]);

        $this->assertTrue((bool) $account1->fresh()->is_default_online_account);

        // 2. Create another default online account
        $account2 = Account::create([
            'name' => 'Account 2',
            'balance' => 2000,
            'is_default_cash_account' => false,
            'is_default_online_account' => true,
        ]);

        // Account 1 should now be false, and Account 2 should be true
        $this->assertFalse((bool) $account1->fresh()->is_default_online_account);
        $this->assertTrue((bool) $account2->fresh()->is_default_online_account);
    }

    /** @test */
    public function updating_an_account_to_default_cash_account_resets_others(): void
    {
        $account1 = Account::create([
            'name' => 'Account 1',
            'balance' => 1000,
            'is_default_cash_account' => true,
            'is_default_online_account' => false,
        ]);

        $account2 = Account::create([
            'name' => 'Account 2',
            'balance' => 2000,
            'is_default_cash_account' => false,
            'is_default_online_account' => false,
        ]);

        $this->assertTrue((bool) $account1->fresh()->is_default_cash_account);
        $this->assertFalse((bool) $account2->fresh()->is_default_cash_account);

        // Update account 2 to be default
        $account2->update(['is_default_cash_account' => true]);

        $this->assertFalse((bool) $account1->fresh()->is_default_cash_account);
        $this->assertTrue((bool) $account2->fresh()->is_default_cash_account);
    }

    /** @test */
    public function updating_an_account_to_default_online_account_resets_others(): void
    {
        $account1 = Account::create([
            'name' => 'Account 1',
            'balance' => 1000,
            'is_default_cash_account' => false,
            'is_default_online_account' => true,
        ]);

        $account2 = Account::create([
            'name' => 'Account 2',
            'balance' => 2000,
            'is_default_cash_account' => false,
            'is_default_online_account' => false,
        ]);

        $this->assertTrue((bool) $account1->fresh()->is_default_online_account);
        $this->assertFalse((bool) $account2->fresh()->is_default_online_account);

        // Update account 2 to be default
        $account2->update(['is_default_online_account' => true]);

        $this->assertFalse((bool) $account1->fresh()->is_default_online_account);
        $this->assertTrue((bool) $account2->fresh()->is_default_online_account);
    }

    /** @test */
    public function controller_store_and_update_methods_correctly_handle_checkboxes(): void
    {
        $user = User::factory()->create();

        // 1. Test Store with checkboxes checked
        $response = $this->actingAs($user)->post(route('accounts.store'), [
            'name' => 'Test Checkbox Account',
            'balance' => 500,
            'is_default_cash_account' => '1',
            'is_default_online_account' => '1',
        ]);

        $response->assertRedirect(route('accounts.index'));
        $account = Account::where('name', 'Test Checkbox Account')->first();
        $this->assertNotNull($account);
        $this->assertTrue((bool) $account->is_default_cash_account);
        $this->assertTrue((bool) $account->is_default_online_account);

        // 2. Test Update with checkboxes unchecked (missing from request)
        $response = $this->actingAs($user)->put(route('accounts.update', $account), [
            'name' => 'Test Checkbox Account Updated',
            'balance' => 600,
            // keys are completely omitted to simulate unchecked state
        ]);

        $response->assertRedirect(route('accounts.index'));
        $account = $account->fresh();
        $this->assertEquals('Test Checkbox Account Updated', $account->name);
        $this->assertEquals(600, $account->balance);
        $this->assertFalse((bool) $account->is_default_cash_account);
        $this->assertFalse((bool) $account->is_default_online_account);
    }
}
