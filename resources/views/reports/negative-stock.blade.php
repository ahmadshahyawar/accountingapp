<x-layouts.app title="اجناس منفی">
    <x-ui.page-header title="اجناس منفی" />

    <p class="text-sm text-gray-500 mb-4">
        جنس هایی که موجودی آن ها منفی شده — یعنی بیشتر از آنچه دریافت شده، فروخته یا انتقال داده شده است.
        این معمولاً نشانه یک اشتباه در ثبت اطلاعات (مثلاً موجودی اول دوره ثبت نشده) است.
    </p>

    <div class="bg-white rounded border border-gray-300 overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr><th class="px-4 py-2">کد جنس</th><th>نام جنس</th><th>واحد</th><th>موجودی</th></tr>
            </thead>
            <tbody class="divide-y">
                @foreach($items as $row)
                    <tr>
                        <td class="px-4 py-2 text-gray-500">{{ $row['item']->code }}</td>
                        <td class="px-4 py-2">{{ $row['item']->name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $row['item']->unit->name }}</td>
                        <td class="px-4 py-2 font-semibold text-rose-700">{{ number_format($row['quantity'], 2) }}</td>
                    </tr>
                @endforeach
                @if($items->isEmpty())
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">جنسی با موجودی منفی وجود ندارد.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</x-layouts.app>
