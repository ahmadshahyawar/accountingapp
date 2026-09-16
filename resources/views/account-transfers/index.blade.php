<x-layouts.app title="انتقال حساب">
    <x-ui.legacy-list title="انتقال حساب ها" :create-route="route('account-transfers.create')" create-label="جدید" table-id="acct-transfers-grid">
        <table class="legacy-grid" id="acct-transfers-grid">
            <thead>
                <tr>
                    <th>تاریخ</th>
                    <th>توضیحات</th>
                    <th>مبلغ</th>
                    <th>ارز</th>
                    <th>از حساب</th>
                    <th>به حساب</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transfers as $t)
                    <tr data-row data-delete-form="delete-acct-transfer-{{ $t->id }}">
                        <td>{{ shamsi($t->date) }}</td>
                        <td>{{ $t->description }}</td>
                        <td>{{ number_format($t->amount, 2) }}</td>
                        <td>{{ $t->currency->code }}</td>
                        <td>{{ $t->fromLabel() }}</td>
                        <td>{{ $t->toLabel() }}</td>
                        <td class="hidden">
                            <form id="delete-acct-transfer-{{ $t->id }}" action="{{ route('account-transfers.destroy', $t) }}" method="POST">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
