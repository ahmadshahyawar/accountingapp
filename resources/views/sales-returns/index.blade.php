<x-layouts.app title="برگشت از فروش">
    <x-ui.page-header title="برگشت از فروش">
        <a href="{{ route('sales-returns.create') }}" class="px-4 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">+ برگشت جدید</a>
    </x-ui.page-header>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">تاریخ</th>
                    <th class="px-4 py-2">فاکتور</th>
                    <th class="px-4 py-2">نام شخص</th>
                    <th class="px-4 py-2">مبلغ کل</th>
                    <th class="px-4 py-2">ارز</th>
                    <th class="px-4 py-2">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($returns as $r)
                    <tr>
                        <td class="px-4 py-2">{{ shamsi($r->date) }}</td>
                        <td class="px-4 py-2">{{ $r->number }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $r->customer->name }}</td>
                        <td class="px-4 py-2">{{ number_format($r->total_amount, 2) }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $r->currency->code }}</td>
                        <td class="px-4 py-2">
                            <form action="{{ route('sales-returns.destroy', $r) }}" method="POST" onsubmit="return confirm('این برگشت حذف شود؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layouts.app>
