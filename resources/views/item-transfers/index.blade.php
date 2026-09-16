<x-layouts.app title="انتقال اجناس">
    <x-ui.page-header title="انتقال اجناس بین گدام‌ها">
        <a href="{{ route('item-transfers.create') }}" class="px-4 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">+ انتقال جدید</a>
    </x-ui.page-header>

    <div class="bg-white rounded border border-gray-300 overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">شماره</th>
                    <th class="px-4 py-2">تاریخ</th>
                    <th class="px-4 py-2">از گدام</th>
                    <th class="px-4 py-2">به گدام</th>
                    <th class="px-4 py-2">تعداد اقلام</th>
                    <th class="px-4 py-2">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($transfers as $t)
                    <tr>
                        <td class="px-4 py-2">{{ $t->number }}</td>
                        <td class="px-4 py-2">{{ shamsi($t->date) }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $t->fromWarehouse->name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $t->toWarehouse->name }}</td>
                        <td class="px-4 py-2">{{ $t->lines()->count() }}</td>
                        <td class="px-4 py-2">
                            <form action="{{ route('item-transfers.destroy', $t) }}" method="POST" onsubmit="return confirm('این انتقال حذف شود؟')">
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
