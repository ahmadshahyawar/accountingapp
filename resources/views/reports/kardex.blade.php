<x-layouts.app title="کاردکس">
    <x-ui.legacy-list title="کاردکس انبار" report-only :search="false" table-id="kardex-grid">
        <x-slot:subtabs>
            @include('reports._nav')
        </x-slot:subtabs>

        <form method="GET" class="legacy-searchrow" style="gap:8px">
            <select name="item_id" required onchange="this.form.submit()" data-searchable style="flex:1">
                <option value="">— جستجوی اجناس —</option>
                @foreach($items as $it)
                    <option value="{{ $it->id }}" @selected($itemId == $it->id)>{{ $it->code }} — {{ $it->name }}</option>
                @endforeach
            </select>
            <select name="warehouse_id" onchange="this.form.submit()" data-searchable style="width:200px">
                <option value="">— همه گدام ها —</option>
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @selected($warehouseId == $warehouse->id)>{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </form>

        @if($item)
            <table class="legacy-grid" id="kardex-grid">
                <thead>
                    <tr>
                        <th>تاریخ</th><th>فاکتور</th><th>خریدار / فروشنده</th><th>گدام</th>
                        <th>ورود جنس</th><th>خروج جنس</th><th>قیمت</th><th>جمع کل</th><th>الباقی</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        @php $move = $row['move']; @endphp
                        <tr data-row>
                            <td>{{ shamsi($move->date) }}</td>
                            <td>{{ $row['number'] }}</td>
                            <td>{{ $row['person']?->name }}</td>
                            <td>{{ $move->warehouse->name }}</td>
                            <td class="text-emerald-700">{{ $move->type === 'in' ? number_format($move->quantity, 2) : '0' }}</td>
                            <td class="text-rose-700">{{ $move->type === 'out' ? number_format($move->quantity, 2) : '0' }}</td>
                            <td>{{ number_format($move->unit_cost, 2) }}</td>
                            <td>{{ number_format($move->quantity * $move->unit_cost, 2) }}</td>
                            <td style="font-weight:700">{{ number_format($row['balance'], 2) }}</td>
                        </tr>
                    @endforeach
                    @if($rows->isEmpty())
                        <tr><td colspan="9" style="text-align:center;color:#9aa3ab;padding:20px">حرکتی برای این جنس ثبت نشده است.</td></tr>
                    @endif
                </tbody>
                @if($rows->isNotEmpty())
                    <tfoot>
                        <tr style="font-weight:700;background:#f2f4f6">
                            <td colspan="4">مجموع</td>
                            <td class="text-emerald-700">{{ number_format($totalIn, 2) }}</td>
                            <td class="text-rose-700">{{ number_format($totalOut, 2) }}</td>
                            <td colspan="2"></td>
                            <td>{{ number_format($rows->last()['balance'], 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        @else
            <div style="text-align:center;color:#9aa3ab;padding:40px">یک جنس را برای نمایش کاردکس انتخاب کنید.</div>
        @endif
    </x-ui.legacy-list>
</x-layouts.app>
