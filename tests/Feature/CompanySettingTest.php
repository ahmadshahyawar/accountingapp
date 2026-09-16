<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Covers مشخصات شرکت (company info) — found via company_info_try3.png, a
 * dialog the old app has that this app never did: name/phone/mobile/email/
 * website/address, meant to appear on invoice letterheads.
 */
class CompanySettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->actingAs(User::where('email', 'admin@example.com')->firstOrFail());
    }

    public function test_company_info_can_be_saved_and_is_shown_on_the_settings_page(): void
    {
        $this->put(route('settings.company.update'), [
            'name' => 'شرکت تجارتی یونیک',
            'phone' => '020-123456',
            'mobile' => '0700111222',
            'email' => 'info@unic.example',
            'website' => 'unic.example',
            'address' => 'هرات، افغانستان',
        ])->assertRedirect();

        $response = $this->get(route('settings.company'));
        $response->assertOk();
        $response->assertSee('شرکت تجارتی یونیک');
        $response->assertSee('info@unic.example');
    }
}
