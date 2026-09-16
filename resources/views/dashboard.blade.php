<x-layouts.app title="داشبورد">
    <h2 class="text-lg font-bold mb-4">دسترسی سریع</h2>

    {{--
        Tile grid — measured pixel-by-pixel against the reference screenshot,
        not eyeballed. It is NOT a uniform 6-column grid: each row is 3
        clusters (two paired tiles + one wide tile) with a small gap inside a
        pair (~9px measured) and a much larger gap between clusters (~66px
        measured, roughly the width of one more tile) — same clustering
        language the ribbon uses. Source order matters here: under dir=rtl
        the first flex/grid child lands on the *right* edge of the screen,
        so every cluster below is listed in real right-to-left reading
        order (confirmed against the reference image's actual left-to-right
        pixel positions), not left-to-right visual order.
        Colors sampled directly from the same screenshot: purple #800080,
        crimson #AE1942, green #00A500, teal #00889E, blue #2875EC, orange
        #D2691E. Icons are hand-built multi-color SVGs (see
        <x-dashboard-icon>) instead of a flat monochrome glyph, since at
        this tile size a single-color icon reads as noticeably cheaper than
        the old app's actual colorful icon art.
    --}}
    <div class="flex gap-4 md:gap-14">
        <a href="{{ route('sales-invoices.index') }}" class="tile flex-1 text-2xl" style="background:#00889E">فروش</a>
        <div class="grid grid-cols-2 gap-2 flex-1">
            <a href="{{ route('cash-vouchers.create', ['type' => 'receipt']) }}" class="tile flex-col gap-1" style="background:#00A500"><x-dashboard-icon name="pay-in" />دریافت نقدی</a>
            <a href="{{ route('cash-vouchers.create', ['type' => 'payment']) }}" class="tile flex-col gap-1" style="background:#AE1942"><x-dashboard-icon name="pay-out" />پرداخت نقدی</a>
        </div>
        <div class="grid grid-cols-2 gap-2 flex-1">
            <a href="{{ route('items.index') }}" class="tile flex-col gap-1" style="background:#D2691E"><x-dashboard-icon name="stock" />موجودی اجناس</a>
            <a href="{{ route('reports.sales-graph') }}" class="tile flex-col gap-1" style="background:#800080"><x-dashboard-icon name="graph" />گراف اجناس پرفروش</a>
        </div>
    </div>

    <div class="flex gap-4 md:gap-14 mt-3">
        <a href="{{ route('purchase-invoices.index') }}" class="tile flex-1 text-2xl" style="background:#D2691E">خرید</a>
        <div class="grid grid-cols-2 gap-2 flex-1">
            <a href="{{ route('reports.day-book') }}" class="tile flex-col gap-1" style="background:#00889E"><x-dashboard-icon name="ledger" />دفتر روزنامچه</a>
            <a href="{{ route('reports.account-statement') }}" class="tile flex-col gap-1" style="background:#2875EC"><x-dashboard-icon name="documents" />گزارش حساب</a>
        </div>
        <div class="grid grid-cols-2 gap-2 flex-1">
            <a href="{{ route('reports.creditors') }}" class="tile flex-col gap-1" style="background:#1D9D51"><x-dashboard-icon name="person-money" />لیست قرضدار ها</a>
            <a href="{{ route('reports.debtors') }}" class="tile flex-col gap-1" style="background:#800080"><x-dashboard-icon name="person-money" />لیست طلبکار ها</a>
        </div>
    </div>

    {{-- Row 3 breaks the "wide tile on the right" pattern rows 1-2 use — confirmed by
         pixel scan, the wide نرخ ارز tile sits on the screen-LEFT here instead, so it's
         last in this row's RTL source order rather than first. --}}
    <div class="flex gap-4 md:gap-14 mt-3">
        <div class="grid grid-cols-2 gap-2 flex-1">
            <a href="{{ route('items.create') }}" class="tile flex-col gap-1" style="background:#1D9D51"><x-dashboard-icon name="box" />تعریف اجناس</a>
            <a href="{{ route('accounts.index') }}" class="tile flex-col gap-1" style="background:#1D9D51"><x-dashboard-icon name="id-card" />تعریف حساب ها</a>
        </div>
        <div class="grid grid-cols-2 gap-2 flex-1">
            <div class="tile flex-col gap-0.5" style="background:#00A500">
                <span class="text-2xl font-bold">{{ $today->format('d') }}</span>
                <span class="text-xs">{{ $today->format('l') }} {{ $today->format('F') }} {{ $today->format('Y') }}</span>
            </div>
            <div class="tile flex-col gap-0.5" style="background:#00A500" x-data x-init="setInterval(() => { $el.querySelector('span').textContent = new Date().toLocaleTimeString('en-GB', {hour:'2-digit', minute:'2-digit'}) }, 1000 * 30)">
                <span class="text-2xl font-bold">{{ now()->format('H:i') }}</span>
            </div>
        </div>
        <a href="{{ route('settings.exchange-rates') }}" class="tile flex-1 flex-col gap-1" style="background:#00889E">
            <x-dashboard-icon name="trend" />
            <span class="text-sm opacity-90">نرخ ارز</span>
            <span class="text-lg font-bold">1 {{ $topExchangeRate?->currency->code ?? '—' }} = {{ $topExchangeRate ? number_format($topExchangeRate->rate, 2) : '—' }} {{ $baseCurrency->code ?? '' }}</span>
        </a>
    </div>

    <div class="flex gap-4 md:gap-14 mt-3">
        <a href="{{ route('settings.company') }}" class="tile flex-1" style="background:#64748B">تنظیمات</a>
        <a href="{{ route('opening-balances.index') }}" class="tile flex-1" style="background:#4B5563">مانده های ابتدایی دوره</a>
    </div>
</x-layouts.app>
