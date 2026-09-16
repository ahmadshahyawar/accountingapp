<x-layouts.app title="تراز آزمایشی">
    <x-ui.legacy-list title="بالانس مالی" report-only :search="false" table-id="trial-balance-grid">
        <x-slot:subtabs>
            @include('reports._nav')
        </x-slot:subtabs>

        <table class="legacy-grid" id="trial-balance-grid">
            <thead>
                <tr><th>کد</th><th>حساب</th><th>مدین</th><th>داین</th><th>مانده</th></tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr data-row>
                        <td>{{ $row['account']->code }}</td>
                        <td>{{ $row['account']->name }}</td>
                        <td>{{ number_format($row['debit'], 2) }}</td>
                        <td>{{ number_format($row['credit'], 2) }}</td>
                        <td style="font-weight:700">{{ number_format($row['balance'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight:700;background:#f2f4f6">
                    <td colspan="2">مجموع</td>
                    <td>{{ number_format($totalDebit, 2) }}</td>
                    <td>{{ number_format($totalCredit, 2) }}</td>
                    <td class="{{ abs($totalDebit - $totalCredit) < 0.01 ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ abs($totalDebit - $totalCredit) < 0.01 ? 'متوازن ✓' : 'عدم توازن!' }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
