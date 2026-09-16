<x-layouts.app title="پیش فاکتور">
    <x-ui.page-header title="پیش فاکتور">
        <a href="{{ route('proforma-invoices.create') }}" class="px-4 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">+ پیش فاکتور جدید</a>
    </x-ui.page-header>

    <div class="bg-white rounded border border-gray-300 overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">شماره</th>
                    <th class="px-4 py-2">تاریخ</th>
                    <th class="px-4 py-2">مشتری</th>
                    <th class="px-4 py-2">تلفن</th>
                    <th class="px-4 py-2">مبلغ کل</th>
                    <th class="px-4 py-2">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($invoices as $inv)
                    <tr>
                        <td class="px-4 py-2">{{ $inv->number }}</td>
                        <td class="px-4 py-2">{{ shamsi($inv->date) }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $inv->customer_name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $inv->customer_phone ?? $inv->customer_mobile }}</td>
                        <td class="px-4 py-2">{{ number_format($inv->total_amount, 2) }}</td>
                        <td class="px-4 py-2">
                            <form action="{{ route('proforma-invoices.destroy', $inv) }}" method="POST" onsubmit="return confirm('این پیش فاکتور حذف شود؟')">
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
