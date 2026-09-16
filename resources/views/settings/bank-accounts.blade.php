<x-layouts.app title="بانک ها">
    <div x-data="{ adding: false }">
        <x-ui.legacy-list title="تعریف بانک ها" create-inline table-id="banks-grid" create-label="بانک جدید">
            <table class="legacy-grid" id="banks-grid">
                <thead>
                    <tr>
                        <th>نام حساب</th>
                        <th>نام بانک</th>
                        <th>شماره حساب</th>
                        <th>واحد پول</th>
                        <th>حساب لجر</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bankAccounts as $bank)
                        <tr data-row>
                            <td>{{ $bank->name }}</td>
                            <td>{{ $bank->bank_name }}</td>
                            <td>{{ $bank->account_number }}</td>
                            <td>{{ $bank->currency->code }}</td>
                            <td>{{ $bank->account->name }}</td>
                        </tr>
                    @endforeach
                    @if($bankAccounts->isEmpty())
                        <tr><td colspan="5" style="text-align:center;color:#9aa3ab;padding:20px">حساب بانکی تعریف نشده است.</td></tr>
                    @endif
                </tbody>
            </table>

            <form action="{{ route('settings.bank-accounts.store') }}" method="POST" x-show="adding" x-cloak class="legacy-searchrow" style="margin-top:10px;gap:8px;flex-wrap:wrap">
                @csrf
                <input name="name" placeholder="نام حساب" required style="flex:1;border:none;background:transparent;padding:4px 8px;font-size:13px">
                <input name="bank_name" placeholder="نام بانک" style="flex:1;border:none;background:transparent;padding:4px 8px;font-size:13px">
                <input name="account_number" placeholder="شماره حساب" style="flex:1;border:none;background:transparent;padding:4px 8px;font-size:13px">
                <select name="currency_id" required data-searchable style="width:120px">
                    @foreach($currencies as $currency)<option value="{{ $currency->id }}">{{ $currency->code }}</option>@endforeach
                </select>
                <select name="account_id" required data-searchable style="width:220px">
                    @foreach($moneyAccounts as $account)<option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>@endforeach
                </select>
                <button class="btn3d" type="submit" style="min-height:30px">افزودن</button>
            </form>
        </x-ui.legacy-list>
    </div>
</x-layouts.app>
