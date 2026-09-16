<x-layouts.app title="صندوق">
    <div x-data="{ adding: false }">
        <x-ui.legacy-list title="تعریف صندوق ها" create-inline table-id="cashboxes-grid">
            <table class="legacy-grid" id="cashboxes-grid">
                <thead>
                    <tr>
                        <th>نام صندوق</th>
                        <th>واحد پول</th>
                        <th>حساب لجر</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cashboxes as $cashbox)
                        <tr data-row>
                            <td>{{ $cashbox->name }}</td>
                            <td>{{ $cashbox->currency->code }}</td>
                            <td>{{ $cashbox->account->name }}</td>
                        </tr>
                    @endforeach
                    @if($cashboxes->isEmpty())
                        <tr><td colspan="3" style="text-align:center;color:#9aa3ab;padding:20px">صندوقی تعریف نشده است.</td></tr>
                    @endif
                </tbody>
            </table>

            <form action="{{ route('settings.cashboxes.store') }}" method="POST" x-show="adding" x-cloak class="legacy-searchrow" style="margin-top:10px;gap:8px">
                @csrf
                <input name="name" placeholder="نام صندوق" required style="flex:1;border:none;background:transparent;padding:4px 8px;font-size:13px">
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
