<x-layouts.app title="نرخ ارز">
    <div x-data="{ adding: false }">
        <x-ui.legacy-list title="نرخ ارز" create-inline create-label="نرخ جدید" table-id="rates-grid">
            <table class="legacy-grid" id="rates-grid">
                <thead>
                    <tr>
                        <th>ارز</th>
                        <th>آخرین نرخ</th>
                        <th>تاریخ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($currencies->where('is_base', false) as $currency)
                        <tr data-row>
                            <td>{{ $currency->code }} — {{ $currency->name }}</td>
                            <td>{{ number_format($currency->exchangeRates->first()->rate ?? 0, 4) }}</td>
                            <td>{{ $currency->exchangeRates->first() ? shamsi($currency->exchangeRates->first()->effective_date) : '—' }}</td>
                        </tr>
                    @endforeach
                    @if($currencies->where('is_base', false)->isEmpty())
                        <tr><td colspan="3" style="text-align:center;color:#9aa3ab;padding:20px">ارز خارجی تعریف نشده است.</td></tr>
                    @endif
                </tbody>
            </table>

            <form action="{{ route('settings.exchange-rates.store') }}" method="POST" x-show="adding" x-cloak class="legacy-searchrow" style="margin-top:10px;gap:8px">
                @csrf
                <select name="currency_id" required data-searchable style="width:120px">
                    @foreach($currencies->where('is_base', false) as $currency)
                        <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                    @endforeach
                </select>
                <input type="number" step="0.0001" name="rate" placeholder="نرخ (به ارز پایه)" required style="flex:1;border:none;background:transparent;padding:4px 8px;font-size:13px">
                <input type="date" name="effective_date" value="{{ now()->toDateString() }}" required style="border:1px solid #d1d5db;border-radius:4px;padding:4px 8px;font-size:13px">
                <button class="btn3d" type="submit" style="min-height:30px">ثبت نرخ</button>
            </form>
        </x-ui.legacy-list>
    </div>
</x-layouts.app>
