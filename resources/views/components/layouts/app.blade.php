@php
    // Mirrors the old app's real ribbon: 7 tabs, each holding a few labeled
    // groups of buttons — compared directly against screenshots of every
    // tab, not guessed. Only links to screens that actually exist in this
    // app; دفتر تفصیل/معین doesn't have a home yet (largely covered
    // by گزارش حساب already) so isn't listed rather than pointing at
    // a 404.
    $ribbonTabs = [
        'home' => [
            'label' => 'اطلاعات اولیه',
            'groups' => [
                'ثبت اطلاعات اول دوره' => [
                    ['label' => 'اول دوره حساب ها', 'route' => 'opening-balances.index'],
                    ['label' => 'اول دوره صندوق و بانک', 'route' => 'opening-balances.index'],
                    ['label' => 'اول دوره اجناس', 'route' => 'opening-balances.index'],
                ],
                'سال مالی' => [
                    ['label' => 'سال مالی', 'route' => 'settings.index', 'hash' => 'fiscal-years'],
                ],
                'ارز' => [
                    ['label' => 'نرخ ارز', 'route' => 'settings.index', 'hash' => 'exchange-rates'],
                    ['label' => 'ارز ها', 'route' => 'settings.index', 'hash' => 'currencies'],
                ],
                'تعریف حساب ها' => [
                    ['label' => 'بانک ها', 'route' => 'settings.index', 'hash' => 'bank-accounts'],
                    ['label' => 'صندوق', 'route' => 'settings.index', 'hash' => 'cashboxes'],
                    ['label' => 'اشخاص', 'route' => 'persons.index'],
                    ['label' => 'حساب ها', 'route' => 'accounts.index'],
                ],
                'تعریف انبار ها و اجناس' => [
                    ['label' => 'واحد ها', 'route' => 'settings.index', 'hash' => 'units'],
                    ['label' => 'انبار ها', 'route' => 'settings.index', 'hash' => 'warehouses'],
                    ['label' => 'اجناس', 'route' => 'items.index'],
                ],
            ],
        ],
        'invoices' => [
            'label' => 'صدور فاکتور',
            'groups' => [
                'خرید و فروش' => [
                    ['label' => 'فاکتور فروش', 'route' => 'sales-invoices.index'],
                    ['label' => 'فاکتور خرید', 'route' => 'purchase-invoices.index'],
                    ['label' => 'برگشت از فروش', 'route' => 'sales-returns.index'],
                    ['label' => 'برگشت از خرید', 'route' => 'purchase-returns.index'],
                    ['label' => 'پیش فاکتور', 'route' => 'proforma-invoices.index'],
                ],
                'متفرقه' => [
                    ['label' => 'انتقال اجناس', 'route' => 'item-transfers.index'],
                ],
            ],
        ],
        'payments' => [
            'label' => 'دریافت و پرداخت',
            'groups' => [
                'دریافت و پرداخت' => [
                    ['label' => 'دریافت نقدی', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'receipt']],
                    ['label' => 'پرداخت نقدی', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'payment']],
                    ['label' => 'جستجوی دریافت و پرداخت', 'route' => 'cash-vouchers.index'],
                ],
                'معاش، مصارف و عواید' => [
                    ['label' => 'ثبت مصارف', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'payment']],
                    ['label' => 'ثبت عواید', 'route' => 'cash-vouchers.create', 'params' => ['type' => 'receipt']],
                ],
                'تبادله و انتقال پول' => [
                    ['label' => 'تبادله ارز', 'route' => 'currency-exchanges.index'],
                    ['label' => 'انتقال پول', 'route' => 'money-transfers.index'],
                    ['label' => 'انتقال حساب', 'route' => 'account-transfers.index'],
                ],
            ],
        ],
        'finreports' => [
            'label' => 'گزارشات مالی',
            'groups' => [
                'گزارشات' => [
                    ['label' => 'گزارش حساب', 'route' => 'reports.account-statement'],
                    ['label' => 'میزان آزمایشی', 'route' => 'reports.trial-balance'],
                    ['label' => 'دفتر روزنامچه', 'route' => 'reports.day-book'],
                    ['label' => 'لیست طلبکار ها', 'route' => 'reports.debtors'],
                    ['label' => 'لیست قرضدار ها', 'route' => 'reports.creditors'],
                ],
            ],
        ],
        'itemreports' => [
            'label' => 'گزارشات اجناس',
            'groups' => [
                'گزارشات' => [
                    ['label' => 'موجودی اجناس', 'route' => 'items.index'],
                    ['label' => 'کاردکس', 'route' => 'reports.kardex'],
                    ['label' => 'گراف اجناس پرفروش', 'route' => 'reports.sales-graph'],
                ],
            ],
        ],
        'tools' => [
            'label' => 'امکانات',
            'groups' => [
                'کاربر' => [
                    ['label' => 'مدیریت کاربر ها', 'route' => 'users.index', 'admin' => true],
                    ['label' => 'تغییر رمز عبور', 'route' => 'profile.password'],
                ],
                'متفرقه' => [
                    ['label' => 'یادداشت و یادآور', 'route' => 'notes.index'],
                    ['label' => 'دفتر تلفن', 'route' => 'persons.phonebook'],
                    ['label' => 'بک آپ اطلاعات', 'route' => 'settings.index', 'hash' => 'backup', 'admin' => true],
                ],
            ],
        ],
        'settings' => [
            'label' => 'تنظیمات',
            'groups' => [
                'تنظیمات' => [
                    ['label' => 'تنظیمات عمومی', 'route' => 'settings.index'],
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
<body class="bg-gray-100 text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col">
        <div x-data="{ activeTab: '{{ $defaultTab }}' }">
            {{-- Title bar --}}
            <div class="bg-white border-b px-4 py-1.5 flex items-center justify-between text-sm">
                <a href="{{ route('dashboard') }}" class="font-bold text-sky-900 hover:underline">سیستم حسابداری یونیک</a>
                <div class="flex items-center gap-3 text-gray-600">
                    <span>کاربر: {{ auth()->user()?->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hover:underline">خروج</button>
                    </form>
                </div>
            </div>

            {{-- Ribbon tab strip --}}
            <div class="bg-gray-50 border-b px-4 flex gap-1 text-sm overflow-x-auto">
                @foreach($ribbonTabs as $key => $tab)
                    <button type="button" @click="activeTab = '{{ $key }}'"
                        class="px-4 py-2 border-b-2 whitespace-nowrap"
                        :class="activeTab === '{{ $key }}' ? 'border-sky-700 text-sky-800 font-semibold bg-white' : 'border-transparent text-gray-600 hover:text-sky-700'">
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </div>

            {{-- Ribbon group panels — one per tab, holding its labeled button groups --}}
            @foreach($ribbonTabs as $key => $tab)
                <div x-show="activeTab === '{{ $key }}'" x-cloak
                    class="bg-slate-800 px-4 py-2 flex gap-6 overflow-x-auto">
                    @foreach($tab['groups'] as $groupLabel => $items)
                        @php $visibleItems = array_filter($items, fn($i) => empty($i['admin']) || auth()->user()?->isAdmin()); @endphp
                        @if(count($visibleItems))
                            <div class="flex flex-col items-center shrink-0">
                                <div class="flex gap-1.5">
                                    @foreach($visibleItems as $item)
                                        <a href="{{ route($item['route'], $item['params'] ?? []) }}{{ isset($item['hash']) ? '#'.$item['hash'] : '' }}"
                                            class="flex flex-col items-center justify-center text-center text-white text-xs bg-slate-700 hover:bg-sky-700 rounded px-2 py-1.5 w-20 h-14 transition">
                                            {{ $item['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                                <div class="text-[11px] text-slate-400 mt-1 border-t border-slate-600 pt-0.5 w-full text-center">{{ $groupLabel }}</div>
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

        <footer class="px-6 py-1.5 text-xs text-gray-500 border-t bg-white flex items-center justify-between">
            <span>&copy; {{ date('Y') }} — نسخه در حال توسعه</span>
            <span>سال مالی: {{ \App\Models\FiscalYear::current()?->name ?? '—' }}</span>
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
