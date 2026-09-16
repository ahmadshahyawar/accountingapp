<x-layouts.app title="صندوق">
    <x-ui.page-header title="صندوق های نقدی" />

    <div class="bg-white rounded border border-gray-300 p-4 max-w-2xl">
        <ul class="text-sm mb-4 divide-y">
            @foreach($cashboxes as $cashbox)
                <li class="py-1.5">{{ $cashbox->name }} — {{ $cashbox->currency->code }} — <span class="text-gray-400">{{ $cashbox->account->name }}</span></li>
            @endforeach
            @if($cashboxes->isEmpty())
                <li class="py-6 text-center text-gray-400">صندوقی تعریف نشده است.</li>
            @endif
        </ul>
        <form action="{{ route('settings.cashboxes.store') }}" method="POST" class="flex flex-wrap gap-2 items-end text-sm border-t pt-4">
            @csrf
            <div><label class="block text-xs text-gray-500 mb-1">نام</label><input name="name" class="border rounded px-2 py-1" required></div>
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
