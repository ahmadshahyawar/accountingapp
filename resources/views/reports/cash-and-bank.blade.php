<x-layouts.app title="گزارش صندوق و بانک">
    <x-ui.legacy-list title="گزارش صندوق و بانک" report-only :search="false" table-id="cash-bank-grid">
        <x-slot:subtabs>
            @include('reports._nav')
        </x-slot:subtabs>

        <div id="cash-bank-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div style="border:1px solid #c7d1db">
                <div style="padding:6px 10px;background:#eaf3fb;color:#20394d;font-weight:700">صندوق های نقدی</div>
                <table class="legacy-grid" style="border:none">
                    <thead><tr><th>صندوق</th><th>ارز</th><th>موجودی</th></tr></thead>
                    <tbody>
                        @foreach($cashboxes as $cashbox)
                            <tr>
                                <td>{{ $cashbox->name }}</td>
                                <td>{{ $cashbox->currency->code }}</td>
                                <td style="font-weight:700">{{ number_format($cashbox->account->balance(), 2) }}</td>
                            </tr>
                        @endforeach
                        @if($cashboxes->isEmpty())
                            <tr><td colspan="3" style="text-align:center;color:#9aa3ab;padding:20px">صندوقی تعریف نشده است.</td></tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:700;background:#f2f4f6"><td colspan="2">مجموع</td><td>{{ number_format($cashboxes->sum(fn($c) => $c->account->balance()), 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>

            <div style="border:1px solid #c7d1db">
                <div style="padding:6px 10px;background:#eaf3fb;color:#20394d;font-weight:700">حساب های بانکی</div>
                <table class="legacy-grid" style="border:none">
                    <thead><tr><th>حساب</th><th>بانک</th><th>ارز</th><th>موجودی</th></tr></thead>
                    <tbody>
                        @foreach($bankAccounts as $bank)
                            <tr>
                                <td>{{ $bank->name }}</td>
                                <td>{{ $bank->bank_name }}</td>
                                <td>{{ $bank->currency->code }}</td>
                                <td style="font-weight:700">{{ number_format($bank->account->balance(), 2) }}</td>
                            </tr>
                        @endforeach
                        @if($bankAccounts->isEmpty())
                            <tr><td colspan="4" style="text-align:center;color:#9aa3ab;padding:20px">حساب بانکی تعریف نشده است.</td></tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr style="font-weight:700;background:#f2f4f6"><td colspan="3">مجموع</td><td>{{ number_format($bankAccounts->sum(fn($b) => $b->account->balance()), 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </x-ui.legacy-list>
</x-layouts.app>
