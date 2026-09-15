<x-layouts.app title="فروش">
    <x-ui.page-header title="فاکتور های فروش">
        <a href="{{ route('sales-invoices.create') }}" class="px-4 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">+ فاکتور فروش جدید</a>
    </x-ui.page-header>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">شماره</th>
                    <th class="px-4 py-2">تاریخ</th>
                    <th class="px-4 py-2">مشتری</th>
                    <th class="px-4 py-2">مبلغ کل</th>
                    <th class="px-4 py-2">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($invoices as $invoice)
                    <tr>
                        <td class="px-4 py-2">{{ $invoice->number }}</td>
                        <td class="px-4 py-2">{{ shamsi($invoice->date) }}</td>
                        <td class="px-4 py-2">{{ $invoice->customer->name }}</td>
                        <td class="px-4 py-2">{{ number_format($invoice->total_amount, 2) }} {{ $invoice->currency->code }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('sales-invoices.show', $invoice) }}" class="text-sky-700 hover:underline">مشاهده</a>
                            <form action="{{ route('sales-invoices.destroy', $invoice) }}" method="POST" onsubmit="return confirm('این فاکتور حذف شود؟')">
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
