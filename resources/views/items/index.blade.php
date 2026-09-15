<x-layouts.app title="اجناس">
    <x-ui.page-header title="تعریف اجناس">
        <a href="{{ route('items.create') }}" class="px-4 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">+ جنس جدید</a>
    </x-ui.page-header>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2">کد</th>
                    <th class="px-4 py-2">نام جنس</th>
                    <th class="px-4 py-2">واحد</th>
                    <th class="px-4 py-2">گدام</th>
                    <th class="px-4 py-2">قیمت خرید</th>
                    <th class="px-4 py-2">قیمت فروش</th>
                    <th class="px-4 py-2">موجودی</th>
                    <th class="px-4 py-2">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($items as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item->code }}</td>
                        <td class="px-4 py-2">{{ $item->name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $item->unit->name }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $item->warehouse?->name }}</td>
                        <td class="px-4 py-2">{{ number_format($item->cost_price, 2) }}</td>
                        <td class="px-4 py-2">{{ number_format($item->sale_price, 2) }}</td>
                        <td class="px-4 py-2 {{ $item->quantityOnHand() <= $item->reorder_level ? 'text-rose-700 font-semibold' : '' }}">
                            {{ number_format($item->quantityOnHand(), 2) }}
                        </td>
                        <td class="px-4 py-2 flex gap-2">
                            <a href="{{ route('items.edit', $item) }}" class="text-sky-700 hover:underline">ویرایش</a>
                            <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('حذف شود؟')">
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
