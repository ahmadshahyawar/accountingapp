<x-layouts.app title="فاکتور خرید {{ $invoice->number }}">
    <x-ui.page-header title="فاکتور خرید {{ $invoice->number }}" :back-route="route('purchase-invoices.index')" />

    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm mb-4">
            <div><div class="text-gray-500">تاریخ</div><div class="font-semibold">{{ shamsi($invoice->date) }}</div></div>
            <div><div class="text-gray-500">تامین‌کننده</div><div class="font-semibold">{{ $invoice->supplier->name }}</div></div>
            <div><div class="text-gray-500">گدام</div><div class="font-semibold">{{ $invoice->warehouse->name }}</div></div>
            <div><div class="text-gray-500">واحد پول</div><div class="font-semibold">{{ $invoice->currency->code }}</div></div>
        </div>

        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr><th class="px-3 py-2">جنس</th><th>مقدار</th><th>قیمت واحد</th><th>تخفیف</th><th>مجموعه</th></tr>
            </thead>
            <tbody class="divide-y">
                @foreach($invoice->lines as $line)
                    <tr>
                        <td class="px-3 py-2">{{ $line->item->name }}</td>
                        <td>{{ number_format($line->quantity, 2) }}</td>
                        <td>{{ number_format($line->unit_price, 2) }}</td>
                        <td>{{ number_format($line->discount, 2) }}</td>
                        <td>{{ number_format($line->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm mt-4 pt-4 border-t">
            <div><div class="text-gray-500">تخفیف</div><div class="font-semibold">{{ number_format($invoice->discount, 2) }}</div></div>
            <div><div class="text-gray-500">مصارف</div><div class="font-semibold">{{ number_format($invoice->expense, 2) }}</div></div>
            <div><div class="text-gray-500">مبلغ کل</div><div class="font-semibold">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency->code }}</div></div>
            <div><div class="text-gray-500">پرداخت نقدی</div><div class="font-semibold">{{ number_format($invoice->paid_amount, 2) }}</div></div>
        </div>

        @if($invoice->notes)
            <p class="text-sm text-gray-500 mt-4">یادداشت: {{ $invoice->notes }}</p>
        @endif
    </div>
</x-layouts.app>
