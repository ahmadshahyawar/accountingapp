<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Covers تغییر رمز عبور — self-service password change for the logged-in
 * user, flagged in app.blade.php as an old-app screen not yet built. This is
 * distinct from an admin editing another user's password via /users.
 */
class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_a_user_can_change_their_own_password_with_the_correct_current_password(): void
    {
        $user = User::where('email', 'admin@example.com')->firstOrFail();
        $this->actingAs($user);

        $this->put(route('profile.password.update'), [
            'current_password' => 'password',
            'password' => 'new-secret-1',
            'password_confirmation' => 'new-secret-1',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('new-secret-1', $user->fresh()->password));
    }

    public function test_the_wrong_current_password_is_rejected_and_nothing_changes(): void
    {
        $user = User::where('email', 'admin@example.com')->firstOrFail();
        $originalHash = $user->password;
        $this->actingAs($user);

        $this->put(route('profile.password.update'), [
            'current_password' => 'not-the-real-password',
            'password' => 'new-secret-1',
            'password_confirmation' => 'new-secret-1',
        ])->assertSessionHasErrors('current_password');

        $this->assertSame($originalHash, $user->fresh()->password);
    }

    public function test_the_form_is_reachable_by_any_authenticated_user_not_just_admins(): void
    {
        $user = User::where('email', 'admin@example.com')->firstOrFail();
        $regular = User::create([
            'name' => 'Regular User', 'email' => 'regular@example.com',
            'password' => Hash::make('password'), 'role' => 'user', 'is_active' => true,
        ]);

        $this->actingAs($regular)->get(route('profile.password'))->assertOk();
    }
}
