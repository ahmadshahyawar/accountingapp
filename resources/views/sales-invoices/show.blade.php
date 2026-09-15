<x-layouts.app title="فاکتور فروش {{ $invoice->number }}">
    <x-ui.page-header title="فاکتور فروش {{ $invoice->number }}" :back-route="route('sales-invoices.index')" />

    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm mb-4">
            <div><div class="text-gray-500">تاریخ</div><div class="font-semibold">{{ $invoice->date->format('Y-m-d') }}</div></div>
            <div><div class="text-gray-500">مشتری</div><div class="font-semibold">{{ $invoice->customer->name }}</div></div>
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
            <tfoot>
                <tr class="font-bold"><td colspan="4" class="px-3 py-2 text-left">مجموع کل</td><td>{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency->code }}</td></tr>
            </tfoot>
        </table>

        @if($invoice->notes)
            <p class="text-sm text-gray-500 mt-4">یادداشت: {{ $invoice->notes }}</p>
        @endif
    </div>
</x-layouts.app>
