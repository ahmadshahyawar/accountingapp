<x-layouts.app title="داشبورد">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold">دسترسی سریع</h2>
            <p class="text-sm text-gray-500">
                سال مالی:
                <span class="font-semibold">{{ $fiscalYear->name ?? 'تعریف نشده' }}</span>
                — امروز: {{ $today->format('d F Y') }}
            </p>
        </div>
        <div class="text-sm text-gray-500 text-left">
            <div>ارز پایه: {{ $baseCurrency->code ?? '—' }}</div>
        </div>
    </div>

    {{-- Quick stats — real numbers once fiscal year / accounts are set up --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded border border-gray-300 p-4">
            <div class="text-sm text-gray-500">مجموع طلب از مشتریان</div>
            <div class="text-2xl font-bold text-emerald-700">{{ number_format($debtorsBalance, 2) }}</div>
            <div class="text-xs text-gray-400">{{ $customerCount }} مشتری</div>
        </div>
        <div class="bg-white rounded border border-gray-300 p-4">
            <div class="text-sm text-gray-500">مجموع قرض به تامین‌کنندگان</div>
            <div class="text-2xl font-bold text-rose-700">{{ number_format($creditorsBalance, 2) }}</div>
            <div class="text-xs text-gray-400">{{ $supplierCount }} تامین‌کننده</div>
        </div>
        <div class="bg-white rounded border border-gray-300 p-4">
            <div class="text-sm text-gray-500">سال مالی جاری</div>
            <div class="text-2xl font-bold">{{ $fiscalYear->name ?? '—' }}</div>
        </div>
    </div>

    {{--
        Quick-access tiles — mirrors the original dashboard's exact 6-column,
        3-row tile grid (three groups of two narrow tiles, each row's third
        group a single wide tile spanning both its columns): row 1 pairs
        graph/stock and receipt/payment with the big فروش tile; row 2 pairs
        the debtor/creditor lists and statement/day-book with the big خرید
        tile; row 3 is the exchange-rate ticker (spanning two columns, same
        as فروش/خرید above it) plus clock/date and the two "define" tiles.
    --}}
    {{-- Tile colors sampled directly from tab_emkanat.png's دسترسی سریع grid: purple #800080, crimson #AE1942, green #00A500, cyan/teal #00889E, blue #2875EC, orange #D2691E. --}}
    <div class="grid grid-cols-2 sm:grid-cols-6 gap-3">
        <a href="{{ route('reports.sales-graph') }}" class="tile" style="background:#800080">گراف اجناس پرفروش</a>
        <a href="{{ route('items.index') }}" class="tile" style="background:#D2691E">موجودی اجناس</a>
        <a href="{{ route('cash-vouchers.create', ['type' => 'payment']) }}" class="tile" style="background:#AE1942">پرداخت نقدی</a>
        <a href="{{ route('cash-vouchers.create', ['type' => 'receipt']) }}" class="tile" style="background:#00A500">دریافت نقدی</a>
        <a href="{{ route('sales-invoices.index') }}" class="tile sm:col-span-2 text-2xl" style="background:#00889E">فروش</a>

        <a href="{{ route('reports.debtors') }}" class="tile" style="background:#800080">لیست طلبکار ها</a>
        <a href="{{ route('reports.creditors') }}" class="tile" style="background:#1D9D51">لیست قرضدار ها</a>
        <a href="{{ route('reports.account-statement') }}" class="tile" style="background:#2875EC">گزارش حساب</a>
        <a href="{{ route('reports.day-book') }}" class="tile" style="background:#00889E">دفتر روزنامچه</a>
        <a href="{{ route('purchase-invoices.index') }}" class="tile sm:col-span-2 text-2xl" style="background:#D2691E">خرید</a>

        <a href="{{ route('settings.index') }}" class="tile sm:col-span-2 flex-col gap-1" style="background:#00889E">
            <span class="text-sm opacity-90">نرخ ارز</span>
            <span class="text-lg font-bold">1 {{ $topExchangeRate?->currency->code ?? '—' }} = {{ $topExchangeRate ? number_format($topExchangeRate->rate, 2) : '—' }} {{ $baseCurrency->code ?? '' }}</span>
        </a>
        <div class="tile flex-col gap-0.5" style="background:#00A500" x-data x-init="setInterval(() => { $el.querySelector('span').textContent = new Date().toLocaleTimeString('en-GB', {hour:'2-digit', minute:'2-digit'}) }, 1000 * 30)">
            <span class="text-2xl font-bold">{{ now()->format('H:i') }}</span>
        </div>
        <div class="tile flex-col gap-0.5" style="background:#00889E">
            <span class="text-2xl font-bold">{{ $today->format('d') }}</span>
            <span class="text-xs">{{ $today->format('l') }} {{ $today->format('F') }} {{ $today->format('Y') }}</span>
        </div>
        <a href="{{ route('accounts.index') }}" class="tile" style="background:#1D9D51">تعریف حساب ها</a>
        <a href="{{ route('items.create') }}" class="tile" style="background:#1D9D51">تعریف اجناس</a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-6 gap-3 mt-3">
        <a href="{{ route('opening-balances.index') }}" class="tile sm:col-span-3" style="background:#4B5563">مانده های ابتدایی دوره</a>
        <a href="{{ route('settings.index') }}" class="tile sm:col-span-3" style="background:#64748B">تنظیمات</a>
    </div>
</x-layouts.app>
