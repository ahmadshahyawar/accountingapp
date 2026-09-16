<x-layouts.app title="انبار ها">
    <x-ui.page-header title="گدام ها" />

    <div class="bg-white rounded border border-gray-300 p-4 max-w-2xl">
        <ul class="text-sm mb-4 divide-y">
            @foreach($warehouses as $warehouse)
                <li class="py-1.5 flex justify-between items-center">
                    <span>{{ $warehouse->name }} @if($warehouse->address)<span class="text-gray-400">— {{ $warehouse->address }}</span>@endif</span>
                    <form action="{{ route('settings.warehouses.destroy', $warehouse) }}" method="POST" onsubmit="return confirm('حذف شود؟')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline text-xs">حذف</button>
                    </form>
                </li>
            @endforeach
            @if($warehouses->isEmpty())
                <li class="py-6 text-center text-gray-400">گدامی تعریف نشده است.</li>
            @endif
        </ul>
        <form action="{{ route('settings.warehouses.store') }}" method="POST" class="flex gap-2 text-sm border-t pt-4">
            @csrf
            <input name="name" placeholder="نام گدام" class="border rounded px-2 py-1 flex-1" required>
            <input name="address" placeholder="آدرس" class="border rounded px-2 py-1 flex-1">
            <button class="px-3 py-1.5 bg-sky-700 text-white rounded">افزودن</button>
        </form>
    </div>
</x-layouts.app>
