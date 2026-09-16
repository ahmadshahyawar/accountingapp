<x-layouts.app title="فروش">
    <x-ui.legacy-list title="فاکتور های فروش" :create-route="route('sales-invoices.create')" create-label="فاکتور جدید" table-id="sales-invoices-grid">
        <table class="legacy-grid" id="sales-invoices-grid">
            <thead>
                <tr>
                    <th>تاریخ</th>
                    <th>فاکتور</th>
                    <th>نام شخص</th>
                    <th>تخفیف</th>
                    <th>مصارف</th>
                    <th>مبلغ کل</th>
                    <th>مبلغ رسید</th>
                    <th>ارز</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                    <tr data-row data-edit-url="{{ route('sales-invoices.show', $invoice) }}" data-delete-form="delete-sales-invoice-{{ $invoice->id }}">
                        <td>{{ shamsi($invoice->date) }}</td>
                        <td>{{ $invoice->number }}</td>
                        <td>{{ $invoice->customer->name }}</td>
                        <td>{{ number_format($invoice->discount, 2) }}</td>
                        <td>{{ number_format($invoice->expense, 2) }}</td>
                        <td>{{ number_format($invoice->total_amount, 2) }}</td>
                        <td>{{ number_format($invoice->paid_amount, 2) }}</td>
                        <td>{{ $invoice->currency->code }}</td>
                        <td class="hidden">
                            <form id="delete-sales-invoice-{{ $invoice->id }}" action="{{ route('sales-invoices.destroy', $invoice) }}" method="POST">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
