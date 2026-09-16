<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

/** یادداشت/یادآور — notes and reminders, an old-app screen this app never had. A note is a reminder once it has a remind_at. */
class NoteController extends Controller
{
    public function index(Request $request)
    {
        $notes = Note::where('user_id', $request->user()->id)
            ->orderBy('is_done')
            ->orderByRaw('remind_at IS NULL')
            ->orderBy('remind_at')
            ->orderByDesc('created_at')
            ->get();

        return view('notes.index', compact('notes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'remind_at' => 'nullable|date',
        ]);

        Note::create($data + ['user_id' => $request->user()->id]);

        return back()->with('success', 'یادداشت ثبت شد.');
    }

    public function toggle(Request $request, Note $note)
    {
        abort_unless($note->user_id === $request->user()->id, 403);

        $note->update(['is_done' => ! $note->is_done]);

        return back();
    }

    public function destroy(Request $request, Note $note)
    {
        abort_unless($note->user_id === $request->user()->id, 403);

        $note->delete();

        return back()->with('success', 'یادداشت حذف شد.');
    }
}
