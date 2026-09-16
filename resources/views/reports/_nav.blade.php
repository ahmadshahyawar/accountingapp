@php $current = request()->route()->getName(); @endphp
<div class="flex gap-2 mb-4 text-sm flex-wrap">
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
    ] as $route => $label)
        <a href="{{ route($route) }}" class="px-3 py-1.5 rounded-md {{ $current === $route ? 'bg-sky-700 text-white' : 'bg-white text-gray-600 border' }}">{{ $label }}</a>
    @endforeach
</div>
