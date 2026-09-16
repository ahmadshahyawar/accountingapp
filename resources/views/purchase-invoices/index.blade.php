<x-layouts.app title="خرید">
    <x-ui.legacy-list title="فاکتور های خرید" :create-route="route('purchase-invoices.create')" create-label="فاکتور جدید" table-id="purchase-invoices-grid">
        <table class="legacy-grid" id="purchase-invoices-grid">
            <thead>
                <tr>
                    <th>تاریخ</th>
                    <th>فاکتور</th>
                    <th>نام شخص</th>
                    <th>تخفیف</th>
                    <th>مصارف</th>
                    <th>مبلغ کل</th>
                    <th>مبلغ پرداخت</th>
                    <th>ارز</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                    <tr data-row data-edit-url="{{ route('purchase-invoices.show', $invoice) }}" data-delete-form="delete-purchase-invoice-{{ $invoice->id }}">
                        <td>{{ shamsi($invoice->date) }}</td>
                        <td>{{ $invoice->number }}</td>
                        <td>{{ $invoice->supplier->name }}</td>
                        <td>{{ number_format($invoice->discount, 2) }}</td>
                        <td>{{ number_format($invoice->expense, 2) }}</td>
                        <td>{{ number_format($invoice->total_amount, 2) }}</td>
                        <td>{{ number_format($invoice->paid_amount, 2) }}</td>
                        <td>{{ $invoice->currency->code }}</td>
                        <td class="hidden">
                            <form id="delete-purchase-invoice-{{ $invoice->id }}" action="{{ route('purchase-invoices.destroy', $invoice) }}" method="POST">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
