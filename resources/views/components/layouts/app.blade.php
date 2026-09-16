@php
    // Mirrors the old app's real ribbon: 7 tabs, each holding a few labeled
    // groups of icon buttons — compared directly against screenshots of every
    // tab (ribbon_tab1-7.png), not guessed. Colors sampled from the actual
    // dashboard/ribbon screenshots (see tab_emkanat.png): workspace #E1E1E1,
    // status bar #0072C6, tile accents purple/crimson/green/teal/blue/orange.
    // Only links to screens that actually exist in this app; دفتر تفصیل/معین
    // doesn't have a home yet (largely covered by گزارش حساب already) so
    // isn't listed rather than pointing at a 404.
    $ribbonTabs = [
        'home' => [
            'label' => 'اطلاعات اولیه',
            'groups' => [
                'تعریف انبار ها و اجناس' => [
                    ['label' => 'اجناس', 'route' => 'items.index', 'icon' => 'items'],
                    ['label' => 'انبار ها', 'route' => 'settings.warehouses', 'icon' => 'warehouses'],
                    ['label' => 'واحد ها', 'route' => 'settings.units', 'icon' => 'units'],
                ],
                'تعریف حساب ها' => [
                    ['label' => 'اشخاص', 'route' => 'persons.index', 'icon' => 'persons'],
                    ['label' => 'کارمندان', 'route' => 'persons.index', 'params' => ['type' => 'employee'], 'icon' => 'employees'],
                    ['label' => 'مصارف', 'route' => 'accounts.index', 'params' => ['type' => 'expense'], 'icon' => 'expense'],
                    ['label' => 'عواید', 'route' => 'accounts.index', 'params' => ['type' => 'revenue'], 'icon' => 'revenue'],
                    ['label' => 'صندوق', 'route' => 'settings.cashboxes', 'icon' => 'cashbox'],
                    ['label' => 'بانک ها', 'route' => 'settings.bank-accounts', 'icon' => 'banks'],
                ],
                'ارز' => [
                    ['label' => 'ارز ها', 'route' => 'settings.currencies', 'icon' => 'currencies'],
                    ['label' => 'نرخ ارز', 'route' => 'settings.exchange-rates', 'icon' => 'exchange-rate'],
                ],
                'سال مالی' => [
                    ['label' => 'سال مالی', 'route' => 'settings.fiscal-years', 'icon' => 'fiscal-year'],
                ],
                'ثبت اطلاعات اول دوره' => [
                    ['label' => 'اول دوره اجناس', 'route' => 'opening-balances.index', 'icon' => 'opening-items'],
                    ['label' => 'اول دوره صندوق و بانک', 'route' => 'opening-balances.index', 'icon' => 'opening-cashbank'],
                    ['label' => 'اول دوره حساب ها', 'route' => 'opening-balances.index', 'icon' => 'opening-accounts'],
                ],
            ],
        ],
        'invoices' => [
            'label' => 'صدور فاکتور',
            'groups' => [
                'خرید و فروش' => [
                    ['label' => 'فاکتور فروش', 'route' => 'sales-invoices.index', 'icon' => 'sales-invoice'],
                    ['label' => 'فاکتور خرید', 'route' => 'purchase-invoices.index', 'icon' => 'purchase-invoice'],
                    ['label' => 'برگشت از فروش', 'route' => 'sales-returns.index', 'icon' => 'sales-return'],
                    ['label' => 'برگشت از خرید', 'route' => 'purchase-returns.index', 'icon' => 'purchase-return'],
                    ['label' => 'پیش فاکتور', 'route' => 'proforma-invoices.index', 'icon' => 'proforma'],
                ],
                'متفرقه' => [
                    ['label' => 'انتقال اجناس', 'route' => 'item-transfers.create', 'icon' => 'item-transfer-new'],
                    ['label' => 'جستجوی فاکتورهای انتقال اجناس', 'route' => 'item-transfers.index', 'icon' => 'search-item-transfer'],
                ],
            ],
        ],
        'payments' => [
            'label' => 'دریافت و پرداخت',
            'groups' => [
                'دریافت و پرداخت' => [
                    ['label' => 'دریافت نقدی', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'receipt'], 'icon' => 'cash-receipt'],
                    ['label' => 'پرداخت نقدی', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'payment'], 'icon' => 'cash-payment'],
                    ['label' => 'جستجوی دریافت و پرداخت', 'route' => 'cash-vouchers.index', 'icon' => 'search-payments'],
                ],
                'معاش، مصارف و عواید' => [
                    ['label' => 'ثبت معاش', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'payment'], 'icon' => 'payroll-entry'],
                    ['label' => 'ثبت مصارف', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'payment'], 'icon' => 'expense-entry'],
                    ['label' => 'ثبت عواید', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'receipt'], 'icon' => 'revenue-entry'],
                ],
                'تبادله و انتقال پول' => [
                    ['label' => 'تبادله ارز', 'route' => 'currency-exchanges.index', 'icon' => 'currency-exchange'],
                    ['label' => 'انتقال پول', 'route' => 'money-transfers.index', 'icon' => 'money-transfer'],
                    ['label' => 'انتقال حساب', 'route' => 'account-transfers.index', 'icon' => 'account-transfer'],
                ],
            ],
        ],
        'finreports' => [
            'label' => 'گزارشات مالی',
            'groups' => [
                // Order verified pixel-by-pixel against the old app's real ribbon screenshot
                // (ribbon_tab3.png) — گزارش حساب rightmost, then قرضدار/طلبکار, then
                // روزنامچه/صندوق‌وبانک, then بالانس. Note قرضدار comes before طلبکار here,
                // and روزنامچه before صندوق‌وبانک — easy to mix up, confirmed from the image.
                'گزارشات مالی' => [
                    ['label' => 'گزارش حساب', 'route' => 'reports.account-statement', 'icon' => 'account-statement'],
                    ['label' => 'لیست قرضدار ها', 'route' => 'reports.creditors', 'icon' => 'creditors-report'],
                    ['label' => 'لیست طلبکار ها', 'route' => 'reports.debtors', 'icon' => 'debtors-report'],
                    ['label' => 'دفتر روزنامچه', 'route' => 'reports.day-book', 'icon' => 'day-book'],
                    ['label' => 'گزارش صندوق و بانک', 'route' => 'reports.cash-and-bank', 'icon' => 'cash-bank-report'],
                    ['label' => 'بالانس مالی', 'route' => 'reports.trial-balance', 'icon' => 'trial-balance'],
                ],
                'گراف' => [
                    ['label' => 'مفاد و ضرر', 'route' => 'reports.profit-and-loss', 'icon' => 'profit-loss-bar'],
                ],
            ],
        ],
        'itemreports' => [
            'label' => 'گزارشات اجناس',
            'groups' => [
                'گزارشات اجناس' => [
                    ['label' => 'موجودی اجناس', 'route' => 'items.index', 'icon' => 'stock-inventory'],
                    ['label' => 'کاردکس', 'route' => 'reports.kardex', 'icon' => 'kardex'],
                    ['label' => 'اجناس منفی', 'route' => 'reports.negative-stock', 'icon' => 'negative-stock'],
                    ['label' => 'فاکتور های فروش', 'route' => 'sales-invoices.index', 'icon' => 'sales-invoice-report'],
                    ['label' => 'فاکتور های خرید', 'route' => 'purchase-invoices.index', 'icon' => 'purchase-invoice-report'],
                ],
                'گراف' => [
                    ['label' => 'گراف اجناس پرفروش', 'route' => 'reports.sales-graph', 'icon' => 'top-items-graph'],
                ],
            ],
        ],
        'tools' => [
            'label' => 'امکانات',
            'groups' => [
                'کاربر' => [
                    ['label' => 'مدیریت کاربر ها', 'route' => 'users.index', 'admin' => true, 'icon' => 'user-management'],
                    ['label' => 'تغییر رمز ورود', 'route' => 'profile.password', 'icon' => 'change-password'],
                ],
                'بک آپ اطلاعات' => [
                    ['label' => 'تهیه بک آپ از اطلاعات', 'route' => 'backup.download', 'admin' => true, 'icon' => 'backup-data'],
                    ['label' => 'بازیابی اطلاعات', 'route' => 'settings.backup', 'admin' => true, 'icon' => 'restore-data'],
                ],
                'ابزار' => [
                    ['label' => 'یادداشت', 'route' => 'notes.index', 'icon' => 'note'],
                    ['label' => 'یادآور', 'route' => 'notes.index', 'icon' => 'reminder'],
                    ['label' => 'دفتر تلفن', 'route' => 'persons.phonebook', 'icon' => 'phonebook'],
                ],
            ],
        ],
        'settings' => [
            'label' => 'تنظیمات',
            'groups' => [
                'تنظیمات' => [
                    ['label' => 'مشخصات شرکت', 'route' => 'settings.company', 'icon' => 'company-info'],
                ],
            ],
        ],
    ];

    // Auto-select the tab whose group contains a route matching the current
    // one, so landing on e.g. /sales-invoices from a bookmark or a redirect
    // opens the ribbon already on "صدور فاکتور" instead of always "اطلاعات اولیه".
    $currentRouteName = request()->route()?->getName();
    $defaultTab = 'home';
    foreach ($ribbonTabs as $key => $tab) {
        foreach ($tab['groups'] as $items) {
            foreach ($items as $item) {
                if ($item['route'] === $currentRouteName) {
                    $defaultTab = $key;
                    break 3;
                }
            }
        }
    }
@endphp
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'حسابداری' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-gray-900 antialiased" style="background:#E1E1E1">
    <div class="min-h-screen flex flex-col">
        <div x-data="{ activeTab: '{{ $defaultTab }}' }" class="bg-white border-b border-gray-300">
            {{-- Title bar + tab strip merged into one row — matches the real app, where the
                 window caption and the ribbon's tab captions sit in the same title-bar row. --}}
            <div class="px-4 flex items-center gap-4 text-sm border-b border-gray-200 overflow-x-auto">
                <a href="{{ route('dashboard') }}" class="font-bold text-sky-900 hover:underline whitespace-nowrap py-2">سیستم حسابداری یونیک</a>
                <div class="flex gap-1 flex-1">
                    @foreach($ribbonTabs as $key => $tab)
                        <button type="button" @click="activeTab = '{{ $key }}'"
                            class="px-4 py-2 border-b-2 whitespace-nowrap transition"
                            :class="activeTab === '{{ $key }}' ? 'border-[#0072C6] text-[#0072C6] font-semibold' : 'border-transparent text-gray-600 hover:text-[#0072C6]'">
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </div>
                <div class="flex items-center gap-3 text-gray-600 whitespace-nowrap">
                    <span>کاربر: {{ auth()->user()?->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hover:underline">خروج</button>
                    </form>
                </div>
            </div>

            {{-- Ribbon group panels — one per tab, holding its labeled icon-button groups --}}
            @foreach($ribbonTabs as $key => $tab)
                <div x-show="activeTab === '{{ $key }}'" x-cloak
                    class="px-4 py-2 flex items-stretch gap-5 overflow-x-auto border-t border-gray-100">
                    @foreach($tab['groups'] as $groupLabel => $items)
                        @php
                            $visibleItems = array_filter($items, fn($i) => empty($i['admin']) || auth()->user()?->isAdmin());
                        @endphp
                        @if(count($visibleItems))
                            <div class="flex flex-col items-center shrink-0 {{ !$loop->first ? 'border-r border-gray-200 pr-5' : '' }}">
                                <div class="flex gap-1">
                                    @foreach($visibleItems as $item)
                                        <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                                            class="flex flex-col items-center justify-start text-center text-gray-700 text-xs leading-tight hover:bg-gray-50 rounded px-1.5 py-1 w-[80px] transition">
                                            <x-ribbon-icon :name="$item['icon'] ?? 'grid'" class="mb-1" />
                                            <span class="line-clamp-2">{{ $item['label'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                                <div class="text-[11px] text-gray-500 mt-auto pt-1 w-full text-center">{{ $groupLabel }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>

        <main class="flex-1 p-6">
            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-300 text-emerald-800 text-sm rounded-md px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-300 text-red-800 text-sm rounded-md px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>

        <footer class="px-6 py-1.5 text-xs text-white flex items-center justify-between" style="background:#0072C6">
            <span>&copy; {{ date('Y') }} — نسخه در حال توسعه</span>
            <span>سال مالی: {{ \App\Models\FiscalYear::current()?->name ?? '—' }}</span>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
