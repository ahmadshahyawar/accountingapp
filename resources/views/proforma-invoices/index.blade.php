<x-layouts.app title="پیش فاکتور">
    <x-ui.legacy-list title="پیش فاکتور" :create-route="route('proforma-invoices.create')" create-label="پیش فاکتور جدید" table-id="proforma-grid">
        <table class="legacy-grid" id="proforma-grid">
            <thead>
                <tr>
                    <th>شماره</th>
                    <th>تاریخ</th>
                    <th>مشتری</th>
                    <th>تلفن</th>
                    <th>مبلغ کل</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $inv)
                    <tr data-row data-delete-form="delete-proforma-{{ $inv->id }}">
                        <td>{{ $inv->number }}</td>
                        <td>{{ shamsi($inv->date) }}</td>
                        <td>{{ $inv->customer_name }}</td>
                        <td>{{ $inv->customer_phone ?? $inv->customer_mobile }}</td>
                        <td>{{ number_format($inv->total_amount, 2) }}</td>
                        <td class="hidden">
                            <form id="delete-proforma-{{ $inv->id }}" action="{{ route('proforma-invoices.destroy', $inv) }}" method="POST">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
