<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Covers یادداشت/یادآور — the last of the old-app screens app.blade.php
 * flagged as not built. A note becomes a reminder just by having a
 * remind_at; there's no separate reminder model.
 */
class NoteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_a_user_can_create_and_list_their_own_notes(): void
    {
        $user = User::where('email', 'admin@example.com')->firstOrFail();
        $this->actingAs($user);

        $this->post(route('notes.store'), ['title' => 'Call the supplier', 'body' => 'about the delayed shipment'])
            ->assertRedirect();

        $this->get(route('notes.index'))->assertOk()->assertSee('Call the supplier');
    }

    public function test_a_regular_non_admin_user_can_delete_their_own_note(): void
    {
        // The app-wide RestrictDestroyToAdmin middleware gates every "*.destroy"
        // route to admins — notes intentionally use a ".remove" route name so a
        // regular user can still delete their own personal notes.
        $regular = User::create([
            'name' => 'Regular User', 'email' => 'regular-notes@example.com',
            'password' => Hash::make('password'), 'role' => 'user', 'is_active' => true,
        ]);
        $note = Note::create(['user_id' => $regular->id, 'title' => 'My private note']);

        $this->actingAs($regular)
            ->delete(route('notes.remove', $note))
            ->assertRedirect();

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }

    public function test_a_user_cannot_delete_or_toggle_another_users_note(): void
    {
        $owner = User::where('email', 'admin@example.com')->firstOrFail();
        $other = User::create([
            'name' => 'Other User', 'email' => 'other-notes@example.com',
            'password' => Hash::make('password'), 'role' => 'user', 'is_active' => true,
        ]);
        $note = Note::create(['user_id' => $owner->id, 'title' => 'Owner only note']);

        $this->actingAs($other)->delete(route('notes.remove', $note))->assertForbidden();
        $this->actingAs($other)->post(route('notes.toggle', $note))->assertForbidden();
        $this->assertDatabaseHas('notes', ['id' => $note->id, 'is_done' => false]);
    }

    public function test_toggling_marks_a_note_done_and_back_again(): void
    {
        $user = User::where('email', 'admin@example.com')->firstOrFail();
        $note = Note::create(['user_id' => $user->id, 'title' => 'Toggle me']);
        $this->actingAs($user);

        $this->post(route('notes.toggle', $note))->assertRedirect();
        $this->assertTrue($note->fresh()->is_done);

        $this->post(route('notes.toggle', $note))->assertRedirect();
        $this->assertFalse($note->fresh()->is_done);
    }
}
