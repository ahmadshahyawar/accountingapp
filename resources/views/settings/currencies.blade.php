<x-layouts.app title="ارز ها">
    <x-ui.page-header title="ارز ها" />

    <div class="bg-white rounded border border-gray-300 p-4">
        <table class="w-full text-sm text-right mb-4">
            <thead class="text-gray-500"><tr><th class="py-1">کد</th><th>نام</th><th>علامت</th><th>پایه</th></tr></thead>
            <tbody class="divide-y">
                @foreach($currencies as $currency)
                    <tr>
                        <td class="py-1">{{ $currency->code }}</td>
                        <td>{{ $currency->name }}</td>
                        <td class="text-gray-500">{{ $currency->symbol }}</td>
                        <td>{{ $currency->is_base ? 'بلی' : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <form action="{{ route('settings.currencies.store') }}" method="POST" class="flex flex-wrap gap-2 items-end text-sm border-t pt-4">
            @csrf
            <div><label class="block text-xs text-gray-500 mb-1">کد</label><input name="code" class="border rounded px-2 py-1 w-20" required></div>
            <div><label class="block text-xs text-gray-500 mb-1">نام</label><input name="name" class="border rounded px-2 py-1" required></div>
            <div><label class="block text-xs text-gray-500 mb-1">علامت</label><input name="symbol" class="border rounded px-2 py-1 w-16"></div>
            <button class="px-3 py-1.5 bg-sky-700 text-white rounded">افزودن</button>
        </form>
    </div>
</x-layouts.app>
