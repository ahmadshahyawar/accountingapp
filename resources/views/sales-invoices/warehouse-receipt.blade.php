<x-layouts.app title="حواله انبار {{ $invoice->number }}">
    <div class="legacy-form-title">حواله انبار — فاکتور {{ $invoice->number }}</div>
    <hr style="border-color:#d7dce1;margin-bottom:14px">

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm mb-4">
        <div><div class="text-gray-500">تاریخ</div><div class="font-semibold">{{ shamsi($invoice->date) }}</div></div>
        <div><div class="text-gray-500">مشتری</div><div class="font-semibold">{{ $invoice->customer->name }}</div></div>
        <div><div class="text-gray-500">گدام</div><div class="font-semibold">{{ $invoice->warehouse->name }}</div></div>
    </div>

    {{-- No prices — a warehouse receipt only tells the storekeeper what to hand over, matching the real app's حواله انبار. --}}
    <table class="legacy-grid" style="margin-bottom:16px">
        <thead>
            <tr><th>کد جنس</th><th>مشخصات جنس</th><th>تعداد</th><th>واحد</th></tr>
        </thead>
        <tbody>
            @foreach($invoice->lines as $line)
                <tr>
                    <td>{{ $line->item->code }}</td>
                    <td>{{ $line->item->name }}</td>
                    <td>{{ number_format($line->quantity, 2) }}</td>
                    <td>{{ $line->item->unit->name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="grid grid-cols-2 gap-8 mt-10 text-sm">
        <div style="border-top:1px solid #b9bfc6;padding-top:6px;text-align:center">امضای تحویل‌دهنده</div>
        <div style="border-top:1px solid #b9bfc6;padding-top:6px;text-align:center">امضای تحویل‌گیرنده</div>
    </div>

    <div class="grid-toolbar" style="margin-top:24px">
        <div class="grp">
            <button type="button" class="btn3d" onclick="window.print()">
                <svg class="ic ic-gray" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 8V3h10v5M5 14h10v3H5v-3zM3 8h14v5h-3M3 8v4h2"/></svg>
                چاپ
            </button>
        </div>
        <div class="grp">
            <a href="{{ route('sales-invoices.show', $invoice) }}" class="btn3d">
                <svg class="ic ic-red" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="9"/><path d="M7 7l6 6M13 7l-6 6"/></svg>
                خروج
            </a>
        </div>
    </div>
</x-layouts.app>
