<x-layouts.app title="انتقال پول">
    <x-ui.page-header title="انتقال پول">
        <a href="{{ route('money-transfers.create') }}" class="px-4 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">+ انتقال جدید</a>
    </x-ui.page-header>

    <div class="bg-white rounded border border-gray-300 overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">شماره</th>
                    <th class="px-4 py-2">تاریخ</th>
                    <th class="px-4 py-2">از</th>
                    <th class="px-4 py-2">به</th>
                    <th class="px-4 py-2">مبلغ</th>
                    <th class="px-4 py-2">توضیحات</th>
                    <th class="px-4 py-2">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($transfers as $t)
                    <tr>
                        <td class="px-4 py-2">{{ $t->number }}</td>
                        <td class="px-4 py-2">{{ shamsi($t->date) }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $t->fromLabel() }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $t->toLabel() }}</td>
                        <td class="px-4 py-2">{{ number_format($t->amount, 2) }} {{ $t->currency->code }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $t->description }}</td>
                        <td class="px-4 py-2">
                            <form action="{{ route('money-transfers.destroy', $t) }}" method="POST" onsubmit="return confirm('این انتقال حذف شود؟')">
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
