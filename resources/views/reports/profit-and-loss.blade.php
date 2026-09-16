<x-layouts.app title="مفاد و ضرر">
    <x-ui.page-header title="گزارش مفاد و ضرر (Profit & Loss)" />

    @include('reports._nav')

    <form method="GET" class="bg-white rounded-lg shadow p-4 mb-4 flex flex-wrap gap-3 items-end text-sm">
        <div><label class="block text-xs text-gray-500 mb-1">از تاریخ</label><input type="date" name="from" value="{{ $from }}" class="border rounded px-2 py-1"></div>
        <div><label class="block text-xs text-gray-500 mb-1">الی تاریخ</label><input type="date" name="to" value="{{ $to }}" class="border rounded px-2 py-1"></div>
        <button class="px-3 py-1.5 bg-sky-700 text-white rounded">نمایش</button>
    </form>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-emerald-50 text-emerald-800 font-bold px-4 py-2">عواید</div>
            <table class="w-full text-sm text-right">
                <tbody class="divide-y">
                    @foreach($revenueAccounts as $row)
                        <tr>
                            <td class="px-4 py-2 text-gray-500">{{ $row['account']->code }}</td>
                            <td class="px-4 py-2">{{ $row['account']->name }}</td>
                            <td class="px-4 py-2">{{ number_format($row['balance'], 2) }}</td>
                        </tr>
                    @endforeach
                    @if($revenueAccounts->isEmpty())
                        <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">عایدی در این دوره ثبت نشده است.</td></tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-gray-50"><td colspan="2" class="px-4 py-2">مجموع عواید</td><td class="px-4 py-2">{{ number_format($totalRevenue, 2) }}</td></tr>
                </tfoot>
            </table>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-rose-50 text-rose-800 font-bold px-4 py-2">مصارف</div>
            <table class="w-full text-sm text-right">
                <tbody class="divide-y">
                    @foreach($expenseAccounts as $row)
                        <tr>
                            <td class="px-4 py-2 text-gray-500">{{ $row['account']->code }}</td>
                            <td class="px-4 py-2">{{ $row['account']->name }}</td>
                            <td class="px-4 py-2">{{ number_format($row['balance'], 2) }}</td>
                        </tr>
                    @endforeach
                    @if($expenseAccounts->isEmpty())
                        <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">مصرفی در این دوره ثبت نشده است.</td></tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-gray-50"><td colspan="2" class="px-4 py-2">مجموع مصارف</td><td class="px-4 py-2">{{ number_format($totalExpense, 2) }}</td></tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 flex items-center justify-between">
        <span class="font-bold text-lg">{{ $netProfit >= 0 ? 'مفاد خالص' : 'ضرر خالص' }}</span>
        <span class="font-bold text-lg {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ number_format(abs($netProfit), 2) }}</span>
    </div>
</x-layouts.app>
