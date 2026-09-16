<x-layouts.app title="بانک ها">
    <x-ui.page-header title="حساب های بانکی" />

    <div class="bg-white rounded border border-gray-300 p-4 max-w-3xl">
        <ul class="text-sm mb-4 divide-y">
            @foreach($bankAccounts as $bank)
                <li class="py-1.5">{{ $bank->name }} ({{ $bank->bank_name }}) — {{ $bank->currency->code }} — <span class="text-gray-400">{{ $bank->account->name }}</span></li>
            @endforeach
            @if($bankAccounts->isEmpty())
                <li class="py-6 text-center text-gray-400">حساب بانکی تعریف نشده است.</li>
            @endif
        </ul>
        <form action="{{ route('settings.bank-accounts.store') }}" method="POST" class="flex flex-wrap gap-2 items-end text-sm border-t pt-4">
            @csrf
            <div><label class="block text-xs text-gray-500 mb-1">نام حساب</label><input name="name" class="border rounded px-2 py-1" required></div>
            <div><label class="block text-xs text-gray-500 mb-1">نام بانک</label><input name="bank_name" class="border rounded px-2 py-1"></div>
            <div><label class="block text-xs text-gray-500 mb-1">شماره حساب</label><input name="account_number" class="border rounded px-2 py-1"></div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">واحد پول</label>
                <select name="currency_id" class="border rounded px-2 py-1" required>
                    @foreach($currencies as $currency)<option value="{{ $currency->id }}">{{ $currency->code }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">حساب لجر</label>
                <select name="account_id" class="border rounded px-2 py-1" required>
                    @foreach($moneyAccounts as $account)<option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>@endforeach
                </select>
            </div>
            <button class="px-3 py-1.5 bg-sky-700 text-white rounded">افزودن</button>
        </form>
    </div>
</x-layouts.app>
