<x-layouts.app title="کاردکس">
    <x-ui.page-header title="کاردکس جنس" />

    @include('reports._nav')

    <form method="GET" class="bg-white rounded border border-gray-300 p-4 mb-4 flex flex-wrap gap-3 items-end text-sm">
        <div>
            <label class="block text-xs text-gray-500 mb-1">جنس</label>
            <select name="item_id" class="border rounded-md px-3 py-2" required onchange="this.form.submit()">
                <option value="">— جستجوی اجناس —</option>
                @foreach($items as $it)
                    <option value="{{ $it->id }}" @selected($itemId == $it->id)>{{ $it->code }} — {{ $it->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">گدام</label>
            <select name="warehouse_id" class="border rounded-md px-3 py-2" onchange="this.form.submit()">
                <option value="">— همه گدام ها —</option>
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" @selected($warehouseId == $warehouse->id)>{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="px-4 py-2 bg-sky-700 text-white rounded-md">نمایش</button>
    </form>

    @if($item)
        <div class="bg-white rounded border border-gray-300 overflow-x-auto">
            <div class="px-4 py-3 border-b text-sm">
                <span class="font-bold">{{ $item->name }}</span>
                <span class="text-gray-400">({{ $item->code }})</span>
                <span class="text-gray-500">— واحد: {{ $item->unit->name }}</span>
            </div>
            <table class="w-full text-sm text-right">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-4 py-2">تاریخ</th><th>فاکتور</th><th>خریدار / فروشنده</th><th>گدام</th>
                        <th>ورود جنس</th><th>خروج جنس</th><th>قیمت</th><th>جمع کل</th><th>الباقی</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($rows as $row)
                        @php $move = $row['move']; @endphp
                        <tr>
                            <td class="px-4 py-2">{{ shamsi($move->date) }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $row['number'] }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $row['person']?->name }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $move->warehouse->name }}</td>
                            <td class="px-4 py-2 text-emerald-700">{{ $move->type === 'in' ? number_format($move->quantity, 2) : '0' }}</td>
                            <td class="px-4 py-2 text-rose-700">{{ $move->type === 'out' ? number_format($move->quantity, 2) : '0' }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ number_format($move->unit_cost, 2) }}</td>
                            <td class="px-4 py-2">{{ number_format($move->quantity * $move->unit_cost, 2) }}</td>
                            <td class="px-4 py-2 font-semibold">{{ number_format($row['balance'], 2) }}</td>
                        </tr>
                    @endforeach
                    @if($rows->isEmpty())
                        <tr><td colspan="9" class="px-4 py-6 text-center text-gray-400">حرکتی برای این جنس ثبت نشده است.</td></tr>
                    @endif
                </tbody>
                @if($rows->isNotEmpty())
                    <tfoot>
                        <tr class="font-bold bg-gray-50">
                            <td class="px-4 py-2" colspan="4">مجموع</td>
                            <td class="px-4 py-2 text-emerald-700">{{ number_format($totalIn, 2) }}</td>
                            <td class="px-4 py-2 text-rose-700">{{ number_format($totalOut, 2) }}</td>
                            <td colspan="2"></td>
                            <td class="px-4 py-2">{{ number_format($rows->last()['balance'], 2) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    @else
        <div class="bg-white rounded border border-gray-300 p-6 text-center text-gray-400 text-sm">یک جنس را برای نمایش کاردکس انتخاب کنید.</div>
    @endif
</x-layouts.app>
