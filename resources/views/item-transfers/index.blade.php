<x-layouts.app title="انتقال اجناس">
    <x-ui.legacy-list title="انتقال اجناس بین گدام‌ها" :create-route="route('item-transfers.create')" create-label="انتقال جدید" table-id="item-transfers-grid">
        <table class="legacy-grid" id="item-transfers-grid">
            <thead>
                <tr>
                    <th>شماره</th>
                    <th>تاریخ</th>
                    <th>از گدام</th>
                    <th>به گدام</th>
                    <th>تعداد اقلام</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transfers as $t)
                    <tr data-row data-delete-form="delete-item-transfer-{{ $t->id }}">
                        <td>{{ $t->number }}</td>
                        <td>{{ shamsi($t->date) }}</td>
                        <td>{{ $t->fromWarehouse->name }}</td>
                        <td>{{ $t->toWarehouse->name }}</td>
                        <td>{{ $t->lines()->count() }}</td>
                        <td class="hidden">
                            <form id="delete-item-transfer-{{ $t->id }}" action="{{ route('item-transfers.destroy', $t) }}" method="POST">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
