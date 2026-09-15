<x-layouts.app title="تنظیمات">
    <x-ui.page-header title="تنظیمات پایه" />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Fiscal years --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-bold mb-3">سال های مالی</h3>
            <table class="w-full text-sm text-right mb-3">
                <thead class="text-gray-500"><tr><th class="py-1">نام</th><th>شروع</th><th>ختم</th><th>وضعیت</th><th></th></tr></thead>
                <tbody class="divide-y">
                    @foreach($fiscalYears as $fy)
                        <tr>
                            <td class="py-1">{{ $fy->name }}</td>
                            <td>{{ shamsi($fy->start_date) }}</td>
                            <td>{{ shamsi($fy->end_date) }}</td>
                            <td>@if($fy->is_current)<span class="text-emerald-700 font-semibold">جاری</span>@endif</td>
                            <td>
                                @unless($fy->is_current)
                                    <form action="{{ route('settings.fiscal-years.activate', $fy) }}" method="POST">
                                        @csrf
                                        <button class="text-sky-700 hover:underline">فعال‌سازی</button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <form action="{{ route('settings.fiscal-years.store') }}" method="POST" class="flex flex-wrap gap-2 items-end text-sm">
                @csrf
                <div><label class="block text-xs text-gray-500 mb-1">نام (مثلاً 1406)</label><input name="name" class="border rounded px-2 py-1 w-24" required></div>
                <div><label class="block text-xs text-gray-500 mb-1">تاریخ شروع</label><input type="date" name="start_date" class="border rounded px-2 py-1" required></div>
                <div><label class="block text-xs text-gray-500 mb-1">تاریخ ختم</label><input type="date" name="end_date" class="border rounded px-2 py-1" required></div>
                <button class="px-3 py-1.5 bg-sky-700 text-white rounded">افزودن</button>
            </form>
        </div>

        {{-- Currencies --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-bold mb-3">واحد های پول و نرخ ارز</h3>
            <table class="w-full text-sm text-right mb-3">
                <thead class="text-gray-500"><tr><th class="py-1">کد</th><th>نام</th><th>پایه</th><th>آخرین نرخ</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($currencies as $currency)
                        <tr>
                            <td class="py-1">{{ $currency->code }}</td>
                            <td>{{ $currency->name }}</td>
                            <td>{{ $currency->is_base ? 'بلی' : '' }}</td>
                            <td>{{ $currency->is_base ? '1.0' : number_format($currency->exchangeRates->first()->rate ?? 0, 4) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <form action="{{ route('settings.currencies.store') }}" method="POST" class="flex flex-wrap gap-2 items-end text-sm mb-3">
                @csrf
                <div><label class="block text-xs text-gray-500 mb-1">کد</label><input name="code" class="border rounded px-2 py-1 w-20" required></div>
                <div><label class="block text-xs text-gray-500 mb-1">نام</label><input name="name" class="border rounded px-2 py-1" required></div>
                <div><label class="block text-xs text-gray-500 mb-1">علامت</label><input name="symbol" class="border rounded px-2 py-1 w-16"></div>
                <button class="px-3 py-1.5 bg-sky-700 text-white rounded">افزودن</button>
            </form>
            <form action="{{ route('settings.exchange-rates.store') }}" method="POST" class="flex flex-wrap gap-2 items-end text-sm border-t pt-3">
                @csrf
                <div>
                    <label class="block text-xs text-gray-500 mb-1">واحد پول</label>
                    <select name="currency_id" class="border rounded px-2 py-1" required>
                        @foreach($currencies->where('is_base', false) as $currency)
                            <option value="{{ $currency->id }}">{{ $currency->code }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-xs text-gray-500 mb-1">نرخ (به ارز پایه)</label><input type="number" step="0.0001" name="rate" class="border rounded px-2 py-1 w-28" required></div>
                <div><label class="block text-xs text-gray-500 mb-1">تاریخ</label><input type="date" name="effective_date" value="{{ now()->toDateString() }}" class="border rounded px-2 py-1" required></div>
                <button class="px-3 py-1.5 bg-sky-700 text-white rounded">ثبت نرخ</button>
            </form>
        </div>

        {{-- Units --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-bold mb-3">واحد های اندازه‌گیری</h3>
            <ul class="text-sm mb-3 divide-y">
                @foreach($units as $unit)
                    <li class="py-1 flex justify-between items-center">
                        <span>{{ $unit->name }} @if($unit->symbol)<span class="text-gray-400">({{ $unit->symbol }})</span>@endif</span>
                        <form action="{{ route('settings.units.destroy', $unit) }}" method="POST" onsubmit="return confirm('حذف شود؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline text-xs">حذف</button>
                        </form>
                    </li>
                @endforeach
            </ul>
            <form action="{{ route('settings.units.store') }}" method="POST" class="flex gap-2 text-sm">
                @csrf
                <input name="name" placeholder="نام واحد" class="border rounded px-2 py-1 flex-1" required>
                <input name="symbol" placeholder="علامت" class="border rounded px-2 py-1 w-20">
                <button class="px-3 py-1.5 bg-sky-700 text-white rounded">افزودن</button>
            </form>
        </div>

        {{-- Warehouses --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-bold mb-3">گدام ها</h3>
            <ul class="text-sm mb-3 divide-y">
                @foreach($warehouses as $warehouse)
                    <li class="py-1 flex justify-between items-center">
                        <span>{{ $warehouse->name }}</span>
                        <form action="{{ route('settings.warehouses.destroy', $warehouse) }}" method="POST" onsubmit="return confirm('حذف شود؟')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline text-xs">حذف</button>
                        </form>
                    </li>
                @endforeach
            </ul>
            <form action="{{ route('settings.warehouses.store') }}" method="POST" class="flex gap-2 text-sm">
                @csrf
                <input name="name" placeholder="نام گدام" class="border rounded px-2 py-1 flex-1" required>
                <input name="address" placeholder="آدرس" class="border rounded px-2 py-1 flex-1">
                <button class="px-3 py-1.5 bg-sky-700 text-white rounded">افزودن</button>
            </form>
        </div>

        {{-- Cashboxes --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-bold mb-3">صندوق های نقدی</h3>
            <ul class="text-sm mb-3 divide-y">
                @foreach($cashboxes as $cashbox)
                    <li class="py-1">{{ $cashbox->name }} — {{ $cashbox->currency->code }} — <span class="text-gray-400">{{ $cashbox->account->name }}</span></li>
                @endforeach
            </ul>
            <form action="{{ route('settings.cashboxes.store') }}" method="POST" class="flex flex-wrap gap-2 items-end text-sm">
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

        {{-- Bank accounts --}}
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-bold mb-3">حساب های بانکی</h3>
            <ul class="text-sm mb-3 divide-y">
                @foreach($bankAccounts as $bank)
                    <li class="py-1">{{ $bank->name }} ({{ $bank->bank_name }}) — {{ $bank->currency->code }} — <span class="text-gray-400">{{ $bank->account->name }}</span></li>
                @endforeach
            </ul>
            <form action="{{ route('settings.bank-accounts.store') }}" method="POST" class="flex flex-wrap gap-2 items-end text-sm">
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
    </div>
</x-layouts.app>
