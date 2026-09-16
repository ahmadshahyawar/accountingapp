<x-layouts.app title="واحد ها">
    <x-ui.page-header title="واحد های اندازه‌گیری" />

    <div class="bg-white rounded border border-gray-300 p-4 max-w-lg">
        <ul class="text-sm mb-4 divide-y">
            @foreach($units as $unit)
                <li class="py-1.5 flex justify-between items-center">
                    <span>{{ $unit->name }} @if($unit->symbol)<span class="text-gray-400">({{ $unit->symbol }})</span>@endif</span>
                    <form action="{{ route('settings.units.destroy', $unit) }}" method="POST" onsubmit="return confirm('حذف شود؟')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline text-xs">حذف</button>
                    </form>
                </li>
            @endforeach
            @if($units->isEmpty())
                <li class="py-6 text-center text-gray-400">واحدی تعریف نشده است.</li>
            @endif
        </ul>
        <form action="{{ route('settings.units.store') }}" method="POST" class="flex gap-2 text-sm border-t pt-4">
            @csrf
            <input name="name" placeholder="نام واحد" class="border rounded px-2 py-1 flex-1" required>
            <input name="symbol" placeholder="علامت" class="border rounded px-2 py-1 w-20">
            <button class="px-3 py-1.5 bg-sky-700 text-white rounded">افزودن</button>
        </form>
    </div>
</x-layouts.app>
