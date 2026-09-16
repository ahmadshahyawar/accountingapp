<x-layouts.app title="گزارش صندوق و بانک">
    <x-ui.page-header title="گزارش صندوق و بانک" />

    @include('reports._nav')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded border border-gray-300 overflow-hidden">
            <div class="bg-sky-50 text-sky-800 font-bold px-4 py-2">صندوق های نقدی</div>
            <table class="w-full text-sm text-right">
                <thead class="bg-gray-50 text-gray-600"><tr><th class="px-4 py-2">صندوق</th><th>ارز</th><th>موجودی</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($cashboxes as $cashbox)
                        <tr>
                            <td class="px-4 py-2">{{ $cashbox->name }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $cashbox->currency->code }}</td>
                            <td class="px-4 py-2 font-semibold">{{ number_format($cashbox->account->balance(), 2) }}</td>
                        </tr>
                    @endforeach
                    @if($cashboxes->isEmpty())
                        <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">صندوقی تعریف نشده است.</td></tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-gray-50"><td colspan="2" class="px-4 py-2">مجموع</td><td class="px-4 py-2">{{ number_format($cashboxes->sum(fn($c) => $c->account->balance()), 2) }}</td></tr>
                </tfoot>
            </table>
        </div>

        <div class="bg-white rounded border border-gray-300 overflow-hidden">
            <div class="bg-sky-50 text-sky-800 font-bold px-4 py-2">حساب های بانکی</div>
            <table class="w-full text-sm text-right">
                <thead class="bg-gray-50 text-gray-600"><tr><th class="px-4 py-2">حساب</th><th>بانک</th><th>ارز</th><th>موجودی</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($bankAccounts as $bank)
                        <tr>
                            <td class="px-4 py-2">{{ $bank->name }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $bank->bank_name }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $bank->currency->code }}</td>
                            <td class="px-4 py-2 font-semibold">{{ number_format($bank->account->balance(), 2) }}</td>
                        </tr>
                    @endforeach
                    @if($bankAccounts->isEmpty())
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">حساب بانکی تعریف نشده است.</td></tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-gray-50"><td colspan="3" class="px-4 py-2">مجموع</td><td class="px-4 py-2">{{ number_format($bankAccounts->sum(fn($b) => $b->account->balance()), 2) }}</td></tr>
                </tfoot>
            </table>
        </div>
    </div>
</x-layouts.app>
