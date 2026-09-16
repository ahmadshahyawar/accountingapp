<x-layouts.app title="کاردکس">
    <x-ui.page-header title="کاردکس جنس" />

    @include('reports._nav')

    @php
        $referenceLabels = [
            'sales_invoice' => 'فاکتور فروش',
            'purchase_invoice' => 'فاکتور خرید',
            'sales_return' => 'برگشت از فروش',
            'purchase_return' => 'برگشت از خرید',
            'item_transfer' => 'انتقال اجناس',
            'opening_balance' => 'اول دوره',
        ];
    @endphp

    <form method="GET" class="bg-white rounded-lg shadow p-4 mb-4 flex flex-wrap gap-3 items-end text-sm">
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
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <div class="px-4 py-3 border-b text-sm">
                <span class="font-bold">{{ $item->name }}</span>
                <span class="text-gray-400">({{ $item->code }})</span>
                <span class="text-gray-500">— واحد: {{ $item->unit->name }}</span>
            </div>
            <table class="w-full text-sm text-right">
                <thead class="bg-gray-50 text-gray-600">
                    <tr><th class="px-4 py-2">تاریخ</th><th>نوع</th><th>مرجع</th><th>گدام</th><th>تعداد</th><th>نرخ</th><th>موجودی</th></tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($rows as $row)
                        @php $move = $row['move']; @endphp
                        <tr>
                            <td class="px-4 py-2">{{ shamsi($move->date) }}</td>
                            <td class="px-4 py-2 {{ $move->type === 'in' ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $move->type === 'in' ? 'ورود' : 'خروج' }}
                            </td>
                            <td class="px-4 py-2 text-gray-500">{{ $referenceLabels[$move->reference_type] ?? $move->reference_type }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $move->warehouse->name }}</td>
                            <td class="px-4 py-2">{{ number_format($move->quantity, 2) }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ number_format($move->unit_cost, 2) }}</td>
                            <td class="px-4 py-2 font-semibold">{{ number_format($row['balance'], 2) }}</td>
                        </tr>
                    @endforeach
                    @if($rows->isEmpty())
                        <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">حرکتی برای این جنس ثبت نشده است.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6 text-center text-gray-400 text-sm">یک جنس را برای نمایش کاردکس انتخاب کنید.</div>
    @endif
</x-layouts.app>
