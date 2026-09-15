<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->seed();
        $this->actingAs(\App\Models\User::where('email', 'admin@example.com')->firstOrFail());

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_an_unauthenticated_visitor_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }
}
