<x-layouts.app title="مانده های ابتدایی دوره">
    <x-ui.page-header title="ثبت اطلاعات اول دوره — سال مالی {{ $fiscalYear->name }}">
        <form action="{{ route('opening-balances.post') }}" method="POST" onsubmit="return confirm('مانده های ذخیره شده به دفتر کل ثبت شوند؟')">
            @csrf
            <button class="px-4 py-2 bg-emerald-700 text-white rounded-md text-sm hover:bg-emerald-800">
                ثبت به دفتر کل
            </button>
        </form>
    </x-ui.page-header>

    @if($alreadyPosted)
        <div class="mb-4 bg-sky-50 border border-sky-300 text-sky-800 text-sm rounded-md px-4 py-3">
            مانده های ابتدایی این سال مالی قبلاً به دفتر کل ثبت شده است. اگر مقادیر پایین را تغییر دهید، دوباره روی «ثبت به دفتر کل» کلیک کنید تا بروزرسانی شود.
        </div>
    @endif

    <p class="text-sm text-gray-500 mb-4">
        این صفحه معادل شروع کار سیستم قدیم است — همان مانده هایی که آنجا ثبت کرده بودید (حساب ها، طلب/قرض مشتریان و تامین‌کنندگان، موجودی اجناس) یک بار اینجا وارد می‌شود.
    </p>

    <div class="space-y-6">
        {{-- Accounts --}}
        <div class="bg-white rounded border border-gray-300 p-4">
            <h3 class="font-bold mb-3">مانده ابتدایی حساب ها</h3>
            <form action="{{ route('opening-balances.accounts') }}" method="POST">
                @csrf
                <table class="w-full text-sm text-right mb-3">
                    <thead class="text-gray-500"><tr><th class="py-1">کد</th><th>حساب</th><th>مدین (Debit)</th><th>داین (Credit)</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($accounts as $i => $account)
                            @php $existing = $accountBalances->get($account->id); @endphp
                            <tr>
                                <td class="py-1">{{ $account->code }}</td>
                                <td>{{ $account->name }}</td>
                                <td>
                                    <input type="hidden" name="lines[{{ $i }}][account_id]" value="{{ $account->id }}">
                                    <input type="number" step="0.01" name="lines[{{ $i }}][debit]" value="{{ $existing->debit ?? 0 }}" class="border rounded px-2 py-1 w-28">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="lines[{{ $i }}][credit]" value="{{ $existing->credit ?? 0 }}" class="border rounded px-2 py-1 w-28">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button class="px-3 py-1.5 bg-sky-700 text-white rounded text-sm">ذخیره مانده حساب ها</button>
            </form>
        </div>

        {{-- Customers --}}
        <div class="bg-white rounded border border-gray-300 p-4">
            <h3 class="font-bold mb-3">مانده ابتدایی مشتریان (طلب از مشتری → حساب {{ $arAccount->name }})</h3>
            <form action="{{ route('opening-balances.persons') }}" method="POST">
                @csrf
                <table class="w-full text-sm text-right mb-3">
                    <thead class="text-gray-500"><tr><th class="py-1">مشتری</th><th>مبلغ طلب</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($customers as $i => $customer)
                            @php $existing = $personBalances->get($customer->id); @endphp
                            <tr>
                                <td class="py-1">{{ $customer->name }}</td>
                                <td>
                                    <input type="hidden" name="lines[{{ $i }}][person_id]" value="{{ $customer->id }}">
                                    <input type="hidden" name="lines[{{ $i }}][role]" value="customer">
                                    <input type="number" step="0.01" name="lines[{{ $i }}][amount]" value="{{ $existing->debit ?? 0 }}" class="border rounded px-2 py-1 w-28">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h4 class="font-semibold mb-2 text-sm">تامین‌کنندگان (قرض به تامین‌کننده → حساب {{ $apAccount->name }})</h4>
                <table class="w-full text-sm text-right mb-3">
                    <thead class="text-gray-500"><tr><th class="py-1">تامین‌کننده</th><th>مبلغ قرض</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($suppliers as $j => $supplier)
                            @php $existing = $personBalances->get($supplier->id); $idx = $customers->count() + $j; @endphp
                            <tr>
                                <td class="py-1">{{ $supplier->name }}</td>
                                <td>
                                    <input type="hidden" name="lines[{{ $idx }}][person_id]" value="{{ $supplier->id }}">
                                    <input type="hidden" name="lines[{{ $idx }}][role]" value="supplier">
                                    <input type="number" step="0.01" name="lines[{{ $idx }}][amount]" value="{{ $existing->credit ?? 0 }}" class="border rounded px-2 py-1 w-28">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button class="px-3 py-1.5 bg-sky-700 text-white rounded text-sm">ذخیره مانده اشخاص</button>
            </form>
        </div>

        {{-- Items --}}
        <div class="bg-white rounded border border-gray-300 p-4">
            <h3 class="font-bold mb-3">موجودی ابتدایی اجناس</h3>
            <form action="{{ route('opening-balances.items') }}" method="POST">
                @csrf
                <table class="w-full text-sm text-right mb-3">
                    <thead class="text-gray-500"><tr><th class="py-1">کد جنس</th><th>نام جنس</th><th>واحد</th><th>گدام</th><th>تعداد</th><th>قیمت خرید</th><th>جمع کل</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($items as $i => $item)
                            @php
                                $existing = $itemBalances->firstWhere('item_id', $item->id);
                                $unitCost = $existing && $existing->quantity ? $existing->debit / $existing->quantity : 0;
                            @endphp
                            <tr>
                                <td class="py-1 text-gray-500">{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td class="text-gray-500">{{ $item->unit->name }}</td>
                                <td>
                                    <select name="lines[{{ $i }}][warehouse_id]" class="border rounded px-2 py-1">
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" @selected(($existing->warehouse_id ?? null) == $warehouse->id)>{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="hidden" name="lines[{{ $i }}][item_id]" value="{{ $item->id }}">
                                    <input type="number" step="0.01" name="lines[{{ $i }}][quantity]" value="{{ $existing->quantity ?? 0 }}" class="border rounded px-2 py-1 w-24">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="lines[{{ $i }}][unit_cost]" value="{{ $unitCost }}" class="border rounded px-2 py-1 w-24">
                                </td>
                                <td class="text-gray-500">{{ number_format(($existing->quantity ?? 0) * $unitCost, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button class="px-3 py-1.5 bg-sky-700 text-white rounded text-sm">ذخیره موجودی اجناس</button>
            </form>
        </div>
    </div>
</x-layouts.app>
