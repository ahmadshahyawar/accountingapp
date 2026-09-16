<x-layouts.app title="انتقال پول">
    <x-ui.legacy-list title="انتقال پول" :create-route="route('money-transfers.create')" create-label="جدید" table-id="money-transfers-grid">
        <table class="legacy-grid" id="money-transfers-grid">
            <thead>
                <tr>
                    <th>تاریخ</th>
                    <th>توضیحات</th>
                    <th>مبلغ</th>
                    <th>ارز</th>
                    <th>از صندوق/بانک</th>
                    <th>به صندوق/بانک</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transfers as $t)
                    <tr data-row data-delete-form="delete-money-transfer-{{ $t->id }}">
                        <td>{{ shamsi($t->date) }}</td>
                        <td>{{ $t->description }}</td>
                        <td>{{ number_format($t->amount, 2) }}</td>
                        <td>{{ $t->currency->code }}</td>
                        <td>{{ $t->fromLabel() }}</td>
                        <td>{{ $t->toLabel() }}</td>
                        <td class="hidden">
                            <form id="delete-money-transfer-{{ $t->id }}" action="{{ route('money-transfers.destroy', $t) }}" method="POST">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
