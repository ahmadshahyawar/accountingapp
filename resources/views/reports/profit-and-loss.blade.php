<x-layouts.app title="مفاد و ضرر">
    <x-ui.legacy-list title="مفاد و ضرر" report-only :search="false" table-id="pl-grid">
        <x-slot:subtabs>
            @include('reports._nav')
        </x-slot:subtabs>

        <form method="GET" class="legacy-searchrow" style="gap:8px">
            <input type="date" name="from" value="{{ $from }}" style="border:1px solid #d1d5db;border-radius:4px;padding:4px 8px;font-size:13px">
            <input type="date" name="to" value="{{ $to }}" style="border:1px solid #d1d5db;border-radius:4px;padding:4px 8px;font-size:13px">
            <button type="submit" class="btn3d" style="min-height:30px">نمایش</button>
        </form>

        <div id="pl-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
            <div style="border:1px solid #c7d1db">
                <div style="padding:6px 10px;background:#e8f7ee;color:#1D9D51;font-weight:700">عواید</div>
                <table class="legacy-grid" style="border:none">
                    <tbody>
                        @foreach($revenueAccounts as $row)
                            <tr>
                                <td style="color:#8a95a1">{{ $row['account']->code }}</td>
                                <td>{{ $row['account']->name }}</td>
                                <td>{{ number_format($row['balance'], 2) }}</td>
                            </tr>
                        @endforeach
                        @if($revenueAccounts->isEmpty())
                            <tr><td colspan="3" style="text-align:center;color:#9aa3ab;padding:20px">عایدی در این دوره ثبت نشده است.</td></tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:700;background:#f2f4f6"><td colspan="2">مجموع عواید</td><td>{{ number_format($totalRevenue, 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>

            <div style="border:1px solid #c7d1db">
                <div style="padding:6px 10px;background:#fdeceb;color:#c0392b;font-weight:700">مصارف</div>
                <table class="legacy-grid" style="border:none">
                    <tbody>
                        @foreach($expenseAccounts as $row)
                            <tr>
                                <td style="color:#8a95a1">{{ $row['account']->code }}</td>
                                <td>{{ $row['account']->name }}</td>
                                <td>{{ number_format($row['balance'], 2) }}</td>
                            </tr>
                        @endforeach
                        @if($expenseAccounts->isEmpty())
                            <tr><td colspan="3" style="text-align:center;color:#9aa3ab;padding:20px">مصرفی در این دوره ثبت نشده است.</td></tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:700;background:#f2f4f6"><td colspan="2">مجموع مصارف</td><td>{{ number_format($totalExpense, 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div style="border:1px solid #c7d1db;padding:12px 16px;display:flex;align-items:center;justify-content:space-between">
            <span style="font-weight:700;font-size:16px">{{ $netProfit >= 0 ? 'مفاد خالص' : 'ضرر خالص' }}</span>
            <span style="font-weight:700;font-size:16px" class="{{ $netProfit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ number_format(abs($netProfit), 2) }}</span>
        </div>
    </x-ui.legacy-list>
</x-layouts.app>
