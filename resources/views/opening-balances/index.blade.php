<x-layouts.app title="مانده های ابتدایی دوره">
    <div class="legacy-form-title">ثبت اطلاعات اول دوره — سال مالی {{ $fiscalYear->name }}</div>
    <hr style="border-color:#d7dce1;margin-bottom:14px">

    <div class="flex justify-end mb-4">
        <form action="{{ route('opening-balances.post') }}" method="POST" onsubmit="return confirm('مانده های ذخیره شده به دفتر کل ثبت شوند؟')">
            @csrf
            <button type="submit" class="btn3d">
                <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 4h10l2 2v10H4V4z"/><path d="M6 4v4h6V4M6 12h8"/></svg>
                ثبت به دفتر کل
            </button>
        </form>
    </div>

    @if($alreadyPosted)
        <div class="mb-4 bg-sky-50 border border-sky-300 text-sky-800 text-sm rounded-md px-4 py-3">
            مانده های ابتدایی این سال مالی قبلاً به دفتر کل ثبت شده است. اگر مقادیر پایین را تغییر دهید، دوباره روی «ثبت به دفتر کل» کلیک کنید تا بروزرسانی شود.
        </div>
    @endif

    <p class="text-sm text-gray-500 mb-4">
        این صفحه معادل شروع کار سیستم قدیم است — همان مانده هایی که آنجا ثبت کرده بودید (حساب ها، طلب/قرض مشتریان و تامین‌کنندگان، موجودی اجناس) یک بار اینجا وارد می‌شود.
    </p>

    <div class="space-y-8">
        {{-- Accounts --}}
        <div>
            <div style="font-weight:700;margin-bottom:8px">مانده ابتدایی حساب ها</div>
            <form action="{{ route('opening-balances.accounts') }}" method="POST">
                @csrf
                <table class="legacy-grid" style="margin-bottom:10px">
                    <thead><tr><th>کد</th><th>حساب</th><th>مدین (Debit)</th><th>داین (Credit)</th></tr></thead>
                    <tbody>
                        @foreach($accounts as $i => $account)
                            @php $existing = $accountBalances->get($account->id); @endphp
                            <tr>
                                <td>{{ $account->code }}</td>
                                <td>{{ $account->name }}</td>
                                <td>
                                    <input type="hidden" name="lines[{{ $i }}][account_id]" value="{{ $account->id }}">
                                    <input type="number" step="0.01" name="lines[{{ $i }}][debit]" value="{{ $existing->debit ?? 0 }}" style="width:110px;border:1px solid #b9bfc6;border-radius:3px;padding:3px 6px;font-size:13px">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="lines[{{ $i }}][credit]" value="{{ $existing->credit ?? 0 }}" style="width:110px;border:1px solid #b9bfc6;border-radius:3px;padding:3px 6px;font-size:13px">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="submit" class="btn3d">
                    <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 4h10l2 2v10H4V4z"/><path d="M6 4v4h6V4M6 12h8"/></svg>
                    ذخیره مانده حساب ها
                </button>
            </form>
        </div>

        <hr style="border-color:#d7dce1">

        {{-- Customers --}}
        <div>
            <div style="font-weight:700;margin-bottom:8px">مانده ابتدایی مشتریان (طلب از مشتری → حساب {{ $arAccount->name }})</div>
            <form action="{{ route('opening-balances.persons') }}" method="POST">
                @csrf
                <table class="legacy-grid" style="margin-bottom:10px">
                    <thead><tr><th>مشتری</th><th>مبلغ طلب</th></tr></thead>
                    <tbody>
                        @foreach($customers as $i => $customer)
                            @php $existing = $personBalances->get($customer->id); @endphp
                            <tr>
                                <td>{{ $customer->name }}</td>
                                <td>
                                    <input type="hidden" name="lines[{{ $i }}][person_id]" value="{{ $customer->id }}">
                                    <input type="hidden" name="lines[{{ $i }}][role]" value="customer">
                                    <input type="number" step="0.01" name="lines[{{ $i }}][amount]" value="{{ $existing->debit ?? 0 }}" style="width:110px;border:1px solid #b9bfc6;border-radius:3px;padding:3px 6px;font-size:13px">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="font-weight:600;font-size:13px;margin-bottom:8px">تامین‌کنندگان (قرض به تامین‌کننده → حساب {{ $apAccount->name }})</div>
                <table class="legacy-grid" style="margin-bottom:10px">
                    <thead><tr><th>تامین‌کننده</th><th>مبلغ قرض</th></tr></thead>
                    <tbody>
                        @foreach($suppliers as $j => $supplier)
                            @php $existing = $personBalances->get($supplier->id); $idx = $customers->count() + $j; @endphp
                            <tr>
                                <td>{{ $supplier->name }}</td>
                                <td>
                                    <input type="hidden" name="lines[{{ $idx }}][person_id]" value="{{ $supplier->id }}">
                                    <input type="hidden" name="lines[{{ $idx }}][role]" value="supplier">
                                    <input type="number" step="0.01" name="lines[{{ $idx }}][amount]" value="{{ $existing->credit ?? 0 }}" style="width:110px;border:1px solid #b9bfc6;border-radius:3px;padding:3px 6px;font-size:13px">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="submit" class="btn3d">
                    <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 4h10l2 2v10H4V4z"/><path d="M6 4v4h6V4M6 12h8"/></svg>
                    ذخیره مانده اشخاص
                </button>
            </form>
        </div>

        <hr style="border-color:#d7dce1">

        {{-- Items --}}
        <div>
            <div style="font-weight:700;margin-bottom:8px">موجودی ابتدایی اجناس</div>
            <form action="{{ route('opening-balances.items') }}" method="POST">
                @csrf
                <table class="legacy-grid" style="margin-bottom:10px">
                    <thead><tr><th>کد جنس</th><th>نام جنس</th><th>واحد</th><th>گدام</th><th>تعداد</th><th>قیمت خرید</th><th>جمع کل</th></tr></thead>
                    <tbody>
                        @foreach($items as $i => $item)
                            @php
                                $existing = $itemBalances->firstWhere('item_id', $item->id);
                                $unitCost = $existing && $existing->quantity ? $existing->debit / $existing->quantity : 0;
                            @endphp
                            <tr>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->unit->name }}</td>
                                <td>
                                    <select name="lines[{{ $i }}][warehouse_id]" data-searchable style="width:150px">
                                        @foreach($warehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" @selected(($existing->warehouse_id ?? null) == $warehouse->id)>{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="hidden" name="lines[{{ $i }}][item_id]" value="{{ $item->id }}">
                                    <input type="number" step="0.01" name="lines[{{ $i }}][quantity]" value="{{ $existing->quantity ?? 0 }}" style="width:90px;border:1px solid #b9bfc6;border-radius:3px;padding:3px 6px;font-size:13px">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="lines[{{ $i }}][unit_cost]" value="{{ $unitCost }}" style="width:90px;border:1px solid #b9bfc6;border-radius:3px;padding:3px 6px;font-size:13px">
                                </td>
                                <td>{{ number_format(($existing->quantity ?? 0) * $unitCost, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <button type="submit" class="btn3d">
                    <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 4h10l2 2v10H4V4z"/><path d="M6 4v4h6V4M6 12h8"/></svg>
                    ذخیره موجودی اجناس
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
