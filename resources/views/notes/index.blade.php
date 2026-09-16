<x-layouts.app title="یادداشت و یادآور">
    <div x-data="{ adding: false }">
        <x-ui.legacy-list title="یادداشت" create-inline table-id="notes-grid">
            <table class="legacy-grid" id="notes-grid">
                <thead>
                    <tr>
                        <th></th>
                        <th>عنوان</th>
                        <th>یادداشت</th>
                        <th>یادآوری</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notes as $note)
                        <tr data-row data-delete-form="delete-note-{{ $note->id }}" class="{{ $note->is_done ? 'opacity-50' : '' }}">
                            <td>
                                <form action="{{ route('notes.toggle', $note) }}" method="POST">
                                    @csrf
                                    <button type="submit" style="width:16px;height:16px;border-radius:3px;border:1px solid #a9aeb5;{{ $note->is_done ? 'background:#1D9D51;color:#fff' : 'background:#fff' }}">{{ $note->is_done ? '✓' : '' }}</button>
                                </form>
                            </td>
                            <td class="{{ $note->is_done ? 'line-through' : '' }}">{{ $note->title }}</td>
                            <td>{{ $note->body }}</td>
                            <td class="{{ $note->remind_at && !$note->is_done && $note->remind_at->isPast() ? 'text-rose-700 font-semibold' : '' }}">
                                @if($note->remind_at) {{ shamsi($note->remind_at, 'Y/m/d') }} — {{ $note->remind_at->format('H:i') }} @endif
                            </td>
                            <td class="hidden">
                                <form id="delete-note-{{ $note->id }}" action="{{ route('notes.remove', $note) }}" method="POST">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:#9aa3ab;padding:20px">یادداشتی ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <form action="{{ route('notes.store') }}" method="POST" x-show="adding" x-cloak class="legacy-searchrow" style="margin-top:10px;gap:8px;flex-wrap:wrap">
                @csrf
                <input name="title" placeholder="عنوان" required style="flex:1;border:none;background:transparent;padding:4px 8px;font-size:13px">
                <input name="body" placeholder="یادداشت" style="flex:2;border:none;background:transparent;padding:4px 8px;font-size:13px">
                <input type="datetime-local" name="remind_at" style="border:1px solid #d1d5db;border-radius:4px;padding:4px 8px;font-size:13px">
                <button class="btn3d" type="submit" style="min-height:30px">افزودن</button>
            </form>
        </x-ui.legacy-list>
    </div>
</x-layouts.app>
