@php $current = request()->route()->getName(); @endphp
<div class="flex justify-end gap-1 mb-2 text-sm flex-wrap">
    @foreach([
        'reports.trial-balance' => 'تراز آزمایشی',
        'reports.account-statement' => 'گزارش حساب',
        'reports.day-book' => 'دفتر روزنامچه',
        'reports.profit-and-loss' => 'مفاد و ضرر',
        'reports.cash-and-bank' => 'گزارش صندوق و بانک',
        'reports.debtors' => 'لیست طلبکار ها',
        'reports.creditors' => 'لیست قرضدار ها',
        'reports.sales-graph' => 'گراف فروش',
        'reports.kardex' => 'کاردکس',
        'reports.negative-stock' => 'اجناس منفی',
    ] as $route => $label)
        <a href="{{ route($route) }}"
            class="px-3 py-1.5 border-t border-x rounded-t {{ $current === $route ? 'bg-white border-gray-300 font-semibold text-sky-800' : 'bg-gray-100 border-transparent text-gray-500 hover:text-sky-700' }}">
            {{ $label }}
        </a>
    @endforeach
</div>
