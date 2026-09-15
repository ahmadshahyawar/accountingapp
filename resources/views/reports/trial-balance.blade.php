<x-layouts.app title="تراز آزمایشی">
    <x-ui.page-header title="تراز آزمایشی (Trial Balance)" />

    @include('reports._nav')

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr><th class="px-4 py-2">کد</th><th>حساب</th><th>مدین</th><th>داین</th><th>مانده</th></tr>
            </thead>
            <tbody class="divide-y">
                @foreach($rows as $row)
                    <tr>
                        <td class="px-4 py-2">{{ $row['account']->code }}</td>
                        <td class="px-4 py-2">{{ $row['account']->name }}</td>
                        <td class="px-4 py-2">{{ number_format($row['debit'], 2) }}</td>
                        <td class="px-4 py-2">{{ number_format($row['credit'], 2) }}</td>
                        <td class="px-4 py-2 font-semibold">{{ number_format($row['balance'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-bold bg-gray-50">
                    <td class="px-4 py-2" colspan="2">مجموع</td>
                    <td class="px-4 py-2">{{ number_format($totalDebit, 2) }}</td>
                    <td class="px-4 py-2">{{ number_format($totalCredit, 2) }}</td>
                    <td class="px-4 py-2 {{ abs($totalDebit - $totalCredit) < 0.01 ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ abs($totalDebit - $totalCredit) < 0.01 ? 'متوازن ✓' : 'عدم توازن!' }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-layouts.app>
