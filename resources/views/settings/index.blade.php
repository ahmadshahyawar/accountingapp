<x-layouts.app title="تنظیمات">
    <x-ui.page-header title="تنظیمات پایه" />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Company info (مشخصات شرکت) — printed on invoice letterheads in the old app. --}}
        <div id="company" class="bg-white rounded-lg shadow p-4 scroll-mt-4 lg:col-span-2">
            <h3 class="font-bold mb-3">مشخصات شرکت</h3>
            <form action="{{ route('settings.company.update') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                @csrf @method('PUT')
                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">نام شرکت</label>
                    <input type="text" name="name" value="{{ old('name', $company->name) }}" class="w-full border rounded-md px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">تلفن</label>
                    <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="w-full border rounded-md px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">موبایل</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $company->mobile) }}" class="w-full border rounded-md px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">ایمیل</label>
                    <input type="email" name="email" value="{{ old('email', $company->email) }}" class="w-full border rounded-md px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">وبسایت</label>
                    <input type="text" name="website" value="{{ old('website', $company->website) }}" class="w-full border rounded-md px-3 py-2">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">آدرس</label>
                    <textarea name="address" rows="2" class="w-full border rounded-md px-3 py-2">{{ old('address', $company->address) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <button class="px-4 py-2 bg-sky-700 text-white rounded-md hover:bg-sky-800">ذخیره</button>
                </div>
            </form>
        </div>

        {{-- Fiscal years --}}
        <div id="fiscal-years" class="bg-white rounded-lg shadow p-4 scroll-mt-4">
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
        <div id="currencies" class="bg-white rounded-lg shadow p-4 scroll-mt-4">
            <h3 class="font-bold mb-3" id="exchange-rates">واحد های پول و نرخ ارز</h3>
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
        <div id="units" class="bg-white rounded-lg shadow p-4 scroll-mt-4">
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
        <div id="warehouses" class="bg-white rounded-lg shadow p-4 scroll-mt-4">
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
        <div id="cashboxes" class="bg-white rounded-lg shadow p-4 scroll-mt-4">
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
        <div id="bank-accounts" class="bg-white rounded-lg shadow p-4 scroll-mt-4">
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

        {{-- Backup / restore — admin only, since restoring replaces the live database file. --}}
        @if(auth()->user()?->isAdmin())
            <div id="backup" class="bg-white rounded-lg shadow p-4 scroll-mt-4 lg:col-span-2">
                <h3 class="font-bold mb-3">پشتیبان‌گیری و بازیابی اطلاعات</h3>
                <div class="flex flex-wrap gap-6 items-start">
                    <div>
                        <p class="text-sm text-gray-500 mb-2">یک نسخه از فایل بانک اطلاعاتی جاری را دانلود کنید.</p>
                        <a href="{{ route('backup.download') }}" class="inline-block px-4 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">
                            دانلود پشتیبان
                        </a>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-2">
                            بازیابی اطلاعات، بانک اطلاعاتی جاری را با فایل انتخابی جایگزین می‌کند
                            (قبل از جایگزینی یک نسخه پشتیبان خودکار نگهداری می‌شود).
                        </p>
                        <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data"
                            onsubmit="return confirm('اطلاعات فعلی با فایل انتخابی جایگزین می‌شود. ادامه می‌دهید؟')"
                            class="flex flex-wrap gap-2 items-center text-sm">
                            @csrf
                            <input type="file" name="backup" accept=".sqlite,.db" required class="border rounded px-2 py-1.5">
                            <button class="px-4 py-2 bg-rose-700 text-white rounded-md text-sm hover:bg-rose-800">بازیابی</button>
                        </form>
                        @error('backup')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        // The ribbon's #section links land before Alpine's x-cloak reveals the active
        // tab panel, so the browser's initial scroll-to-anchor lands short — redo it
        // once Alpine has finished and the real layout height is known.
        document.addEventListener('alpine:initialized', () => {
            if (location.hash) {
                document.querySelector(location.hash)?.scrollIntoView();
            }
        });
    </script>
    @endpush
</x-layouts.app>
