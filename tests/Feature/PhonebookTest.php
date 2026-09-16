<?php

namespace Tests\Feature;

use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers دفتر تلفن — another old-app screen flagged as not built. Just a
 * filtered view over Person, which already carries phone/mobile/address.
 */
class PhonebookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'admin@example.com')->firstOrFail());
    }

    public function test_only_persons_with_a_phone_or_mobile_number_are_listed(): void
    {
        Person::create(['name' => 'Has Phone', 'phone' => '0201234567']);
        Person::create(['name' => 'Has Mobile', 'mobile' => '0700111222']);
        Person::create(['name' => 'No Contact Info']);

        $response = $this->get(route('persons.phonebook'));

        $response->assertOk();
        $response->assertSee('Has Phone');
        $response->assertSee('Has Mobile');
        $response->assertDontSee('No Contact Info');
    }
}
