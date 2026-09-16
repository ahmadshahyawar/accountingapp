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
                'ثبت اطلاعات اول دوره' => [
                    ['label' => 'اول دوره حساب ها', 'route' => 'opening-balances.index', 'icon' => 'clipboard'],
                    ['label' => 'اول دوره صندوق و بانک', 'route' => 'opening-balances.index', 'icon' => 'bank'],
                    ['label' => 'اول دوره اجناس', 'route' => 'opening-balances.index', 'icon' => 'cube'],
                ],
                'سال مالی' => [
                    ['label' => 'سال مالی', 'route' => 'settings.fiscal-years', 'icon' => 'calendar'],
                ],
                'ارز' => [
                    ['label' => 'نرخ ارز', 'route' => 'settings.exchange-rates', 'icon' => 'exchange'],
                    ['label' => 'ارز ها', 'route' => 'settings.currencies', 'icon' => 'currency'],
                ],
                'تعریف حساب ها' => [
                    ['label' => 'بانک ها', 'route' => 'settings.bank-accounts', 'icon' => 'bank'],
                    ['label' => 'صندوق', 'route' => 'settings.cashboxes', 'icon' => 'archive'],
                    ['label' => 'عواید', 'route' => 'accounts.index', 'params' => ['type' => 'revenue'], 'icon' => 'currency'],
                    ['label' => 'مصارف', 'route' => 'accounts.index', 'params' => ['type' => 'expense'], 'icon' => 'cash-out'],
                    ['label' => 'کارمندان', 'route' => 'persons.index', 'params' => ['type' => 'employee'], 'icon' => 'user-group'],
                    ['label' => 'اشخاص', 'route' => 'persons.index', 'icon' => 'users'],
                    ['label' => 'حساب ها', 'route' => 'accounts.index', 'icon' => 'clipboard'],
                ],
                'تعریف انبار ها و اجناس' => [
                    ['label' => 'واحد ها', 'route' => 'settings.units', 'icon' => 'scale'],
                    ['label' => 'انبار ها', 'route' => 'settings.warehouses', 'icon' => 'warehouse'],
                    ['label' => 'اجناس', 'route' => 'items.index', 'icon' => 'cube'],
                ],
            ],
        ],
        'invoices' => [
            'label' => 'صدور فاکتور',
            'groups' => [
                'خرید و فروش' => [
                    ['label' => 'فاکتور فروش', 'route' => 'sales-invoices.index', 'icon' => 'cart'],
                    ['label' => 'فاکتور خرید', 'route' => 'purchase-invoices.index', 'icon' => 'box'],
                    ['label' => 'برگشت از فروش', 'route' => 'sales-returns.index', 'icon' => 'return'],
                    ['label' => 'برگشت از خرید', 'route' => 'purchase-returns.index', 'icon' => 'return'],
                    ['label' => 'پیش فاکتور', 'route' => 'proforma-invoices.index', 'icon' => 'document'],
                ],
                'متفرقه' => [
                    ['label' => 'انتقال اجناس', 'route' => 'item-transfers.create', 'icon' => 'transfer'],
                    ['label' => 'جستجوی فاکتورهای انتقال اجناس', 'route' => 'item-transfers.index', 'icon' => 'search'],
                ],
            ],
        ],
        'payments' => [
            'label' => 'دریافت و پرداخت',
            'groups' => [
                'دریافت و پرداخت' => [
                    ['label' => 'دریافت نقدی', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'receipt'], 'icon' => 'cash-in'],
                    ['label' => 'پرداخت نقدی', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'payment'], 'icon' => 'cash-out'],
                    ['label' => 'جستجوی دریافت و پرداخت', 'route' => 'cash-vouchers.index', 'icon' => 'search'],
                ],
                'معاش، مصارف و عواید' => [
                    ['label' => 'ثبت معاش', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'payment'], 'icon' => 'user-group'],
                    ['label' => 'ثبت مصارف', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'payment'], 'icon' => 'cash-out'],
                    ['label' => 'ثبت عواید', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'receipt'], 'icon' => 'currency'],
                ],
                'تبادله و انتقال پول' => [
                    ['label' => 'تبادله ارز', 'route' => 'currency-exchanges.index', 'icon' => 'exchange'],
                    ['label' => 'انتقال پول', 'route' => 'money-transfers.index', 'icon' => 'transfer'],
                    ['label' => 'انتقال حساب', 'route' => 'account-transfers.index', 'icon' => 'transfer'],
                ],
            ],
        ],
        'finreports' => [
            'label' => 'گزارشات مالی',
            'groups' => [
                'گزارشات مالی' => [
                    ['label' => 'لیست قرضدار ها', 'route' => 'reports.creditors', 'icon' => 'user-group'],
                    ['label' => 'لیست طلبکار ها', 'route' => 'reports.debtors', 'icon' => 'users'],
                    ['label' => 'گزارش صندوق و بانک', 'route' => 'reports.cash-and-bank', 'icon' => 'bank'],
                    ['label' => 'دفتر روزنامچه', 'route' => 'reports.day-book', 'icon' => 'clipboard'],
                    ['label' => 'بالانس مالی', 'route' => 'reports.trial-balance', 'icon' => 'scale'],
                    ['label' => 'گزارش حساب', 'route' => 'reports.account-statement', 'icon' => 'document'],
                ],
                'گراف' => [
                    ['label' => 'مفاد و ضرر', 'route' => 'reports.profit-and-loss', 'icon' => 'chart'],
                ],
            ],
        ],
        'itemreports' => [
            'label' => 'گزارشات اجناس',
            'groups' => [
                'گزارشات اجناس' => [
                    ['label' => 'موجودی اجناس', 'route' => 'items.index', 'icon' => 'cube'],
                    ['label' => 'کاردکس', 'route' => 'reports.kardex', 'icon' => 'clipboard'],
                    ['label' => 'اجناس منفی', 'route' => 'reports.negative-stock', 'icon' => 'exclamation'],
                    ['label' => 'فاکتور های فروش', 'route' => 'sales-invoices.index', 'icon' => 'cart'],
                    ['label' => 'فاکتور های خرید', 'route' => 'purchase-invoices.index', 'icon' => 'box'],
                ],
                'گراف' => [
                    ['label' => 'گراف اجناس پرفروش', 'route' => 'reports.sales-graph', 'icon' => 'chart'],
                ],
            ],
        ],
        'tools' => [
            'label' => 'امکانات',
            'groups' => [
                'کاربر' => [
                    ['label' => 'مدیریت کاربر ها', 'route' => 'users.index', 'admin' => true, 'icon' => 'user-group'],
                    ['label' => 'تغییر رمز ورود', 'route' => 'profile.password', 'icon' => 'key'],
                ],
                'بک آپ اطلاعات' => [
                    ['label' => 'تهیه بک آپ از اطلاعات', 'route' => 'backup.download', 'admin' => true, 'icon' => 'cloud-down'],
                    ['label' => 'بازیابی اطلاعات', 'route' => 'settings.backup', 'admin' => true, 'icon' => 'cloud-up'],
                ],
                'ابزار' => [
                    ['label' => 'یادداشت', 'route' => 'notes.index', 'icon' => 'pencil'],
                    ['label' => 'یادآور', 'route' => 'notes.index', 'icon' => 'bell'],
                    ['label' => 'دفتر تلفن', 'route' => 'persons.phonebook', 'icon' => 'phone'],
                ],
            ],
        ],
        'settings' => [
            'label' => 'تنظیمات',
            'groups' => [
                'تنظیمات' => [
                    ['label' => 'مشخصات شرکت', 'route' => 'settings.company', 'icon' => 'building'],
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
                                            class="flex flex-col items-center justify-start text-center text-gray-700 text-[11px] leading-tight hover:bg-gray-50 rounded px-1.5 py-1 w-[72px] transition">
                                            <x-ribbon-icon :name="$item['icon'] ?? 'grid'" class="text-3xl leading-none mb-1" />
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
