<x-layouts.app title="یادداشت و یادآور">
    <x-ui.page-header title="یادداشت و یادآور" />

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form action="{{ route('notes.store') }}" method="POST" class="flex flex-wrap gap-3 items-end text-sm">
            @csrf
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs text-gray-500 mb-1">عنوان<span class="text-red-600">*</span></label>
                <input type="text" name="title" required class="w-full border rounded-md px-3 py-2">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-gray-500 mb-1">یادداشت</label>
                <input type="text" name="body" class="w-full border rounded-md px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">یادآوری در (اختیاری)</label>
                <input type="datetime-local" name="remind_at" class="border rounded-md px-3 py-2">
            </div>
            <button class="px-4 py-2 bg-sky-700 text-white rounded-md hover:bg-sky-800">افزودن</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow divide-y">
        @forelse($notes as $note)
            <div class="p-4 flex items-start gap-3 {{ $note->is_done ? 'opacity-50' : '' }}">
                <form action="{{ route('notes.toggle', $note) }}" method="POST" class="pt-0.5">
                    @csrf
                    <button type="submit" class="w-5 h-5 rounded border flex items-center justify-center {{ $note->is_done ? 'bg-emerald-600 border-emerald-600 text-white' : 'border-gray-300' }}">
                        @if($note->is_done) ✓ @endif
                    </button>
                </form>
                <div class="flex-1">
                    <div class="font-semibold {{ $note->is_done ? 'line-through' : '' }}">{{ $note->title }}</div>
                    @if($note->body)
                        <div class="text-sm text-gray-500">{{ $note->body }}</div>
                    @endif
                    @if($note->remind_at)
                        <div class="text-xs mt-1 {{ !$note->is_done && $note->remind_at->isPast() ? 'text-rose-700 font-semibold' : 'text-sky-700' }}">
                            یادآوری: {{ shamsi($note->remind_at, 'Y/m/d') }} — {{ $note->remind_at->format('H:i') }}
                            @if(!$note->is_done && $note->remind_at->isPast()) (سررسید شده) @endif
                        </div>
                    @endif
                </div>
                <form action="{{ route('notes.remove', $note) }}" method="POST" onsubmit="return confirm('حذف شود؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline text-xs">حذف</button>
                </form>
            </div>
        @empty
            <div class="p-6 text-center text-gray-400 text-sm">یادداشتی ثبت نشده است.</div>
        @endforelse
    </div>
</x-layouts.app>
