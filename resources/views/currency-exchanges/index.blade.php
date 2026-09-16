<x-layouts.app title="تبادله ارز">
    <x-ui.legacy-list title="تبادله ارز" :create-route="route('currency-exchanges.create')" create-label="جدید" table-id="exchanges-grid">
        <table class="legacy-grid" id="exchanges-grid">
            <thead>
                <tr>
                    <th>تاریخ</th>
                    <th>صندوق/بانک</th>
                    <th>پرداخت شده</th>
                    <th>دریافت شده</th>
                    <th>سود/زیان</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exchanges as $e)
                    <tr data-row data-delete-form="delete-exchange-{{ $e->id }}">
                        <td>{{ shamsi($e->date) }}</td>
                        <td>{{ $e->holderLabel() }}</td>
                        <td>{{ number_format($e->paid_amount, 2) }} {{ $e->paidCurrency->code }}</td>
                        <td>{{ number_format($e->received_amount, 2) }} {{ $e->receivedCurrency->code }}</td>
                        <td class="{{ $e->gainLoss() >= 0 ? 'text-green-700' : 'text-red-700' }}">{{ number_format($e->gainLoss(), 2) }}</td>
                        <td class="hidden">
                            <form id="delete-exchange-{{ $e->id }}" action="{{ route('currency-exchanges.destroy', $e) }}" method="POST">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
