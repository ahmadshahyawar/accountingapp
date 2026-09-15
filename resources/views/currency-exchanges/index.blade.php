<x-layouts.app title="تبادله ارز">
    <x-ui.page-header title="تبادله ارز">
        <a href="{{ route('currency-exchanges.create') }}" class="px-4 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">+ تبادله جدید</a>
    </x-ui.page-header>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">شماره</th>
                    <th class="px-4 py-2">تاریخ</th>
                    <th class="px-4 py-2">صندوق/بانک</th>
                    <th class="px-4 py-2">پرداخت شده</th>
                    <th class="px-4 py-2">دریافت شده</th>
                    <th class="px-4 py-2">سود/زیان</th>
                    <th class="px-4 py-2">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($exchanges as $e)
                    <tr>
                        <td class="px-4 py-2">{{ $e->number }}</td>
                        <td class="px-4 py-2">{{ $e->date->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $e->holderLabel() }}</td>
                        <td class="px-4 py-2">{{ number_format($e->paid_amount, 2) }} {{ $e->paidCurrency->code }}</td>
                        <td class="px-4 py-2">{{ number_format($e->received_amount, 2) }} {{ $e->receivedCurrency->code }}</td>
                        <td class="px-4 py-2 {{ $e->gainLoss() >= 0 ? 'text-green-700' : 'text-red-700' }}">{{ number_format($e->gainLoss(), 2) }}</td>
                        <td class="px-4 py-2">
                            <form action="{{ route('currency-exchanges.destroy', $e) }}" method="POST" onsubmit="return confirm('این تبادله حذف شود؟')">
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
