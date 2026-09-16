<x-layouts.app title="اجناس">
    <x-ui.legacy-list title="تعریف اجناس" :create-route="route('items.create')" table-id="items-grid">
        <table class="legacy-grid" id="items-grid">
            <thead>
                <tr>
                    <th>کد جنس</th>
                    <th>نام جنس</th>
                    <th>واحد</th>
                    <th>گدام</th>
                    <th>قیمت خرید</th>
                    <th>قیمت فروش</th>
                    <th>موجودی</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    <tr data-row data-edit-url="{{ route('items.edit', $item) }}" data-delete-form="delete-item-{{ $item->id }}">
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->unit->name }}</td>
                        <td>{{ $item->warehouse?->name }}</td>
                        <td>{{ number_format($item->cost_price, 2) }}</td>
                        <td>{{ number_format($item->sale_price, 2) }}</td>
                        <td class="{{ $item->quantityOnHand() <= $item->reorder_level ? 'text-rose-700 font-semibold' : '' }}">
                            {{ number_format($item->quantityOnHand(), 2) }}
                        </td>
                        <td class="hidden">
                            <form id="delete-item-{{ $item->id }}" action="{{ route('items.destroy', $item) }}" method="POST">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
