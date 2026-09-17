<x-layouts.app title="رسید نقدی">
    <form action="{{ route('cash-vouchers.store') }}" method="POST"
        x-data="{
            source: 'cashbox', contraType: 'person',
            persons: @js($persons->map(fn($p) => ['id' => $p->id, 'name' => $p->name])),
            balances: @js($balances), personId: '{{ old('person_id') }}',
            amount: {{ old('amount', 0) }},
            get previousBalance() { return this.personId && this.balances[this.personId] ? this.balances[this.personId] : 0 },
            get remainingBalance() { return {{ $type === 'receipt' ? 'true' : 'false' }} ? this.previousBalance - (parseFloat(this.amount) || 0) : this.previousBalance + (parseFloat(this.amount) || 0) },
        }">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

        <x-ui.legacy-form :title="$type === 'receipt' ? 'دریافت نقدی' : 'پرداخت نقدی'" :back-route="route('cash-vouchers.index')" save-label="ذخیره">
            <x-slot:extraButtons>
                <button type="button" class="btn3d" onclick="window.print()">
                    <svg class="ic ic-gray" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 8V3h10v5M5 14h10v3H5v-3zM3 8h14v5h-3M3 8v4h2"/></svg>
                    چاپ رسید
                </button>
            </x-slot:extraButtons>

            <div class="legacy-field">
                <label>حساب</label>
                <input type="hidden" name="person_id" x-show="contraType === 'person'" :value="personId">
                <div x-show="contraType === 'person'" style="display:flex;gap:8px">
                    <x-ui.person-search-modal list="persons" selected="personId" :create-route="route('persons.store')" />
                    <input type="text" readonly :value="persons.find(p => p.id == personId)?.name || ''" style="flex:1;border:1px solid #b9bfc6;border-radius:3px;padding:6px 8px;font-size:13px;background:#f2f4f6;color:#586470">
                </div>
                <div x-show="contraType === 'account'" x-cloak>
                    <select name="contra_account_id" data-searchable>
                        <option value="">— انتخاب —</option>
                        @foreach(($type === 'receipt' ? $revenueAccounts : $expenseAccounts) as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-4 text-sm mt-2">
                    <label class="flex items-center gap-1"><input type="radio" x-model="contraType" value="person" checked> شخص (مشتری/تامین‌کننده)</label>
                    <label class="flex items-center gap-1"><input type="radio" x-model="contraType" value="account"> حساب {{ $type === 'receipt' ? 'عواید' : 'مصرف' }}</label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="legacy-field">
                    <label>صندوق / بانک</label>
                    <div x-show="source === 'cashbox'">
                        <select name="cashbox_id" data-searchable>
                            <option value="">— انتخاب —</option>
                            @foreach($cashboxes as $cashbox)
                                <option value="{{ $cashbox->id }}">{{ $cashbox->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="source === 'bank'" x-cloak>
                        <select name="bank_account_id" data-searchable>
                            <option value="">— انتخاب —</option>
                            @foreach($bankAccounts as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-4 text-sm mt-2">
                        <label class="flex items-center gap-1"><input type="radio" x-model="source" value="cashbox" checked> صندوق نقدی</label>
                        <label class="flex items-center gap-1"><input type="radio" x-model="source" value="bank"> بانک</label>
                    </div>
                </div>
                <div class="legacy-field">
                    <label>تاریخ<span class="text-red-600">*</span></label>
                    <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required>
                </div>
            </div>

            <hr style="border-color:#d7dce1;margin:14px 0">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-ui.field label="مبلغ" name="amount" type="number" step="0.01" />
                <x-ui.field label="ارز" name="currency_id" type="select" :options="$currencies->pluck('code', 'id')->all()" required />
                <x-ui.field label="نرخ ارز" name="fx_rate" type="number" step="0.0001" :value="old('fx_rate', 1)" required />
            </div>

            <x-ui.field label="توضیحات" name="description" type="textarea" />

            <hr style="border-color:#d7dce1;margin:14px 0">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="contraType === 'person'">
                <div class="legacy-field" style="margin-bottom:0">
                    <label>حساب گذشته</label>
                    <input type="text" readonly :value="previousBalance.toFixed(2)" style="background:#f2f4f6;color:#586470;text-align:left">
                </div>
                <div class="legacy-field" style="margin-bottom:0">
                    <label>الباقی حساب</label>
                    <input type="text" readonly :value="remainingBalance.toFixed(2)" style="background:#f2f4f6;color:#586470;text-align:left;font-weight:700">
                </div>
            </div>
        </x-ui.legacy-form>
    </form>
</x-layouts.app>
