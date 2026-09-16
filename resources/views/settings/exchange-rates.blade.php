<x-layouts.app title="نرخ ارز">
    <x-ui.page-header title="نرخ ارز" />

    <div class="bg-white rounded border border-gray-300 p-4">
        <table class="w-full text-sm text-right mb-4">
            <thead class="text-gray-500"><tr><th class="py-1">ارز</th><th>آخرین نرخ</th><th>تاریخ</th></tr></thead>
            <tbody class="divide-y">
                @foreach($currencies->where('is_base', false) as $currency)
                    <tr>
                        <td class="py-1">{{ $currency->code }} — {{ $currency->name }}</td>
                        <td>{{ number_format($currency->exchangeRates->first()->rate ?? 0, 4) }}</td>
                        <td class="text-gray-500">{{ $currency->exchangeRates->first() ? shamsi($currency->exchangeRates->first()->effective_date) : '—' }}</td>
                    </tr>
                @endforeach
                @if($currencies->where('is_base', false)->isEmpty())
                    <tr><td colspan="3" class="py-6 text-center text-gray-400">ارز خارجی تعریف نشده است.</td></tr>
                @endif
            </tbody>
        </table>
        <form action="{{ route('settings.exchange-rates.store') }}" method="POST" class="flex flex-wrap gap-2 items-end text-sm border-t pt-4">
            @csrf
            <div>
                <label class="block text-xs text-gray-500 mb-1">واحد پول</label>
                <select name="currency_id" class="border rounded px-2 py-1" required>
                    @foreach($currencies->where('is_base', false) as $currency)
                        <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="block text-xs text-gray-500 mb-1">نرخ (به ارز پایه)</label><input type="number" step="0.0001" name="rate" class="border rounded px-2 py-1 w-28" required></div>
            <div><label class="block text-xs text-gray-500 mb-1">تاریخ</label><input type="date" name="effective_date" value="{{ now()->toDateString() }}" class="border rounded px-2 py-1" required></div>
            <button class="px-3 py-1.5 bg-sky-700 text-white rounded">ثبت نرخ</button>
        </form>
    </div>
</x-layouts.app>
