<x-layouts.app title="برگشت از فروش">
    <x-ui.legacy-list title="برگشت از فروش" :create-route="route('sales-returns.create')" create-label="برگشت جدید" table-id="sales-returns-grid">
        <table class="legacy-grid" id="sales-returns-grid">
            <thead>
                <tr>
                    <th>تاریخ</th>
                    <th>فاکتور</th>
                    <th>نام شخص</th>
                    <th>مبلغ کل</th>
                    <th>ارز</th>
                </tr>
            </thead>
            <tbody>
                @foreach($returns as $r)
                    <tr data-row data-delete-form="delete-sales-return-{{ $r->id }}">
                        <td>{{ shamsi($r->date) }}</td>
                        <td>{{ $r->number }}</td>
                        <td>{{ $r->customer->name }}</td>
                        <td>{{ number_format($r->total_amount, 2) }}</td>
                        <td>{{ $r->currency->code }}</td>
                        <td class="hidden">
                            <form id="delete-sales-return-{{ $r->id }}" action="{{ route('sales-returns.destroy', $r) }}" method="POST">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
