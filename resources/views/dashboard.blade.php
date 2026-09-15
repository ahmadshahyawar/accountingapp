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
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">مجموع طلب از مشتریان</div>
            <div class="text-2xl font-bold text-emerald-700">{{ number_format($debtorsBalance, 2) }}</div>
            <div class="text-xs text-gray-400">{{ $customerCount }} مشتری</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">مجموع قرض به تامین‌کنندگان</div>
            <div class="text-2xl font-bold text-rose-700">{{ number_format($creditorsBalance, 2) }}</div>
            <div class="text-xs text-gray-400">{{ $supplierCount }} تامین‌کننده</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">سال مالی جاری</div>
            <div class="text-2xl font-bold">{{ $fiscalYear->name ?? '—' }}</div>
        </div>
    </div>

    {{-- Quick-access tiles — mirrors the original dashboard's module grid. --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @foreach ([
            ['label' => 'فروش', 'color' => 'bg-sky-600', 'route' => 'sales-invoices.index'],
            ['label' => 'خرید', 'color' => 'bg-orange-600', 'route' => 'purchase-invoices.index'],
            ['label' => 'دریافت نقدی', 'color' => 'bg-green-600', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'receipt']],
            ['label' => 'پرداخت نقدی', 'color' => 'bg-pink-700', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'payment']],
            ['label' => 'لیست طلبکار ها', 'color' => 'bg-purple-700', 'route' => 'reports.debtors'],
            ['label' => 'لیست قرضدار ها', 'color' => 'bg-green-700', 'route' => 'reports.creditors'],
            ['label' => 'موجودی اجناس', 'color' => 'bg-orange-500', 'route' => 'items.index'],
            ['label' => 'گراف اجناس فروش', 'color' => 'bg-purple-500', 'route' => 'reports.sales-graph'],
            ['label' => 'گزارش حساب', 'color' => 'bg-blue-500', 'route' => 'reports.account-statement'],
            ['label' => 'دفتر روزنامچه', 'color' => 'bg-blue-700', 'route' => 'reports.day-book'],
            ['label' => 'تعریف حساب ها', 'color' => 'bg-teal-600', 'route' => 'accounts.index'],
            ['label' => 'تعریف اجناس', 'color' => 'bg-red-600', 'route' => 'items.create'],
            ['label' => 'مانده های ابتدایی دوره', 'color' => 'bg-gray-700', 'route' => 'opening-balances.index'],
            ['label' => 'تنظیمات', 'color' => 'bg-slate-500', 'route' => 'settings.index'],
        ] as $tile)
            <a href="{{ route($tile['route'], $tile['params'] ?? []) }}" class="{{ $tile['color'] }} text-white rounded-lg shadow p-6 flex items-center justify-center text-center font-semibold hover:opacity-90 transition">
                {{ $tile['label'] }}
            </a>
        @endforeach
    </div>
</x-layouts.app>
