<x-layouts.app title="فاکتور فروش {{ $invoice->number }}">
    <div class="legacy-form-title">فاکتور فروش {{ $invoice->number }}</div>
    <hr style="border-color:#d7dce1;margin-bottom:14px">

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm mb-4">
        <div><div class="text-gray-500">تاریخ</div><div class="font-semibold">{{ shamsi($invoice->date) }}</div></div>
        <div><div class="text-gray-500">مشتری</div><div class="font-semibold">{{ $invoice->customer->name }}</div></div>
        <div><div class="text-gray-500">گدام</div><div class="font-semibold">{{ $invoice->warehouse->name }}</div></div>
        <div><div class="text-gray-500">واحد پول</div><div class="font-semibold">{{ $invoice->currency->code }}</div></div>
    </div>

    <table class="legacy-grid" style="margin-bottom:8px">
        <thead>
            <tr><th>جنس</th><th>مقدار</th><th>قیمت واحد</th><th>تخفیف</th><th>مجموعه</th></tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $line)
                <tr>
                    <td>{{ $line->item->name }}</td>
                    <td>{{ number_format($line->quantity, 2) }}</td>
                    <td>{{ number_format($line->unit_price, 2) }}</td>
                    <td>{{ number_format($line->discount, 2) }}</td>
                    <td>{{ number_format($line->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm mt-4 mb-6">
        <div><div class="text-gray-500">تخفیف</div><div class="font-semibold">{{ number_format($invoice->discount, 2) }}</div></div>
        <div><div class="text-gray-500">مصارف</div><div class="font-semibold">{{ number_format($invoice->expense, 2) }}</div></div>
        <div><div class="text-gray-500">مبلغ کل</div><div class="font-semibold">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency->code }}</div></div>
        <div><div class="text-gray-500">دریافت نقدی</div><div class="font-semibold">{{ number_format($invoice->paid_amount, 2) }}</div></div>
    </div>

    @if($invoice->notes)
        <p class="text-sm text-gray-500 mb-4">یادداشت: {{ $invoice->notes }}</p>
    @endif

    <div class="grid-toolbar">
        <div class="grp">
            <form action="{{ route('sales-invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('این فاکتور حذف شود؟')">
                @csrf @method('DELETE')
                <button type="submit" class="btn3d">
                    <svg class="ic ic-red" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="9"/><path d="M7 7l6 6M13 7l-6 6"/></svg>
                    حذف فاکتور
                </button>
            </form>
        </div>
        <div class="grp">
            <a href="{{ route('sales-invoices.warehouse-receipt', $invoice) }}" class="btn3d">
                <svg class="ic ic-gray" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 8V3h10v5M5 14h10v3H5v-3zM3 8h14v5h-3M3 8v4h2"/></svg>
                چاپ حواله انبار
            </a>
            <button type="button" class="btn3d" onclick="window.print()">
                <svg class="ic ic-gray" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 8V3h10v5M5 14h10v3H5v-3zM3 8h14v5h-3M3 8v4h2"/></svg>
                چاپ
            </button>
            <a href="{{ route('sales-invoices.index') }}" class="btn3d">
                <svg class="ic ic-red" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="9"/><path d="M7 7l6 6M13 7l-6 6"/></svg>
                خروج
            </a>
        </div>
    </div>
</x-layouts.app>
