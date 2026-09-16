<x-layouts.app title="رسید نقدی">
    <x-ui.page-header :title="$type === 'receipt' ? 'دریافت نقدی' : 'پرداخت نقدی'" :back-route="route('cash-vouchers.index')" />

    <form action="{{ route('cash-vouchers.store') }}" method="POST" class="bg-white rounded border border-gray-300 p-6 max-w-2xl"
        x-data="{
            source: 'cashbox', contraType: 'person',
            balances: @js($balances), personId: '{{ old('person_id') }}',
            amount: {{ old('amount', 0) }},
            get previousBalance() { return this.personId && this.balances[this.personId] ? this.balances[this.personId] : 0 },
            get remainingBalance() { return {{ $type === 'receipt' ? 'true' : 'false' }} ? this.previousBalance - (parseFloat(this.amount) || 0) : this.previousBalance + (parseFloat(this.amount) || 0) },
        }">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

        <div class="flex justify-between items-start mb-4">
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">تاریخ<span class="text-red-600">*</span></label>
                <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border">
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">شماره رسید</label>
                <input type="text" value="{{ $nextNumber }}" readonly
                    class="w-full rounded-md border-gray-200 bg-gray-50 shadow-sm text-sm py-2 px-3 border text-gray-500 text-left">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">صندوق / بانک</label>
            <div class="flex gap-4 text-sm mb-2">
                <label class="flex items-center gap-1"><input type="radio" x-model="source" value="cashbox" checked> صندوق نقدی</label>
                <label class="flex items-center gap-1"><input type="radio" x-model="source" value="bank"> بانک</label>
            </div>
            <div x-show="source === 'cashbox'">
                <x-ui.field label="صندوق" name="cashbox_id" type="select"
                    :options="['' => '— انتخاب —'] + $cashboxes->pluck('name', 'id')->all()" />
            </div>
            <div x-show="source === 'bank'" x-cloak>
                <x-ui.field label="حساب بانکی" name="bank_account_id" type="select"
                    :options="['' => '— انتخاب —'] + $bankAccounts->pluck('name', 'id')->all()" />
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">حساب</label>
            <div class="flex gap-4 text-sm mb-2">
                <label class="flex items-center gap-1"><input type="radio" x-model="contraType" value="person" checked> شخص (مشتری/تامین‌کننده)</label>
                <label class="flex items-center gap-1"><input type="radio" x-model="contraType" value="account"> حساب {{ $type === 'receipt' ? 'عواید' : 'مصرف' }}</label>
            </div>

            <div x-show="contraType === 'person'">
                <select name="person_id" x-model="personId" class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border">
                    <option value="">— جستجوی حساب ها —</option>
                    @foreach($persons as $person)
                        <option value="{{ $person->id }}" @selected(old('person_id') == $person->id)>{{ $person->name }}</option>
                    @endforeach
                </select>
            </div>
            <div x-show="contraType === 'account'" x-cloak>
                <x-ui.field label="حساب" name="contra_account_id" type="select"
                    :options="['' => '— انتخاب —'] + ($type === 'receipt' ? $revenueAccounts : $expenseAccounts)->pluck('name', 'id')->all()" />
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">مبلغ<span class="text-red-600">*</span></label>
                <input type="number" step="0.01" name="amount" x-model.number="amount" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ارز<span class="text-red-600">*</span></label>
                <select name="currency_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border">
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" @selected(old('currency_id') == $currency->id)>{{ $currency->code }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">نرخ ارز<span class="text-red-600">*</span></label>
                <input type="number" step="0.0001" name="fx_rate" value="{{ old('fx_rate', 1) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border">
            </div>
        </div>

        <x-ui.field label="توضیحات" name="description" type="textarea" />

        <div class="grid grid-cols-2 gap-6 mb-6 pt-2 border-t" x-show="contraType === 'person'">
            <div class="flex justify-between text-sm pt-3"><span class="text-gray-600">حساب گذشته:</span><span x-text="previousBalance.toFixed(2)"></span></div>
            <div class="flex justify-between text-sm font-bold pt-3"><span>الباقی حساب:</span><span x-text="remainingBalance.toFixed(2)"></span></div>
        </div>

        <div class="flex items-center justify-between border-t pt-4">
            <a href="{{ route('cash-vouchers.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300">انصراف</a>
            <button type="submit" class="px-8 py-2 {{ $type === 'receipt' ? 'bg-green-700 hover:bg-green-800' : 'bg-pink-700 hover:bg-pink-800' }} text-white rounded-md text-sm">
                ذخیره
            </button>
        </div>
    </form>
</x-layouts.app>
