<x-layouts.app title="لیست طلبکار ها">
    <x-ui.legacy-list title="لیست طلبکار ها" report-only table-id="debtors-grid">
        <x-slot:subtabs>
            @include('reports._nav')
        </x-slot:subtabs>

        <table class="legacy-grid" id="debtors-grid">
            <thead>
                <tr><th>نام شخص</th><th>مبلغ</th><th>ارز</th><th>موبایل</th><th>تلفن</th><th>آدرس</th></tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr data-row>
                        <td>{{ $row['person']->name }}</td>
                        <td class="{{ $row['balance'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}" style="font-weight:700">{{ number_format($row['balance'], 2) }}</td>
                        <td>{{ $baseCurrency?->code }}</td>
                        <td>{{ $row['person']->mobile ?? '—' }}</td>
                        <td>{{ $row['person']->phone ?? '—' }}</td>
                        <td>{{ $row['person']->address ?? '—' }}</td>
                    </tr>
                @endforeach
                @if($rows->isEmpty())
                    <tr><td colspan="6" style="text-align:center;color:#9aa3ab;padding:20px">مانده ای موجود نیست.</td></tr>
                @endif
            </tbody>
            <tfoot>
                <tr style="font-weight:700;background:#f2f4f6"><td>مجموع</td><td>{{ number_format($rows->sum('balance'), 2) }}</td><td colspan="4"></td></tr>
            </tfoot>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
