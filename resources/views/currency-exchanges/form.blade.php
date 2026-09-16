<x-layouts.app title="تبادله ارز">
    <form action="{{ route('currency-exchanges.store') }}" method="POST"
        x-data="{
            holderType: 'cashbox',
            paidAmount: {{ old('paid_amount', 0) }}, paidRate: {{ old('paid_rate', 1) }},
            receivedRate: {{ old('received_rate', 1) }},
            get equivalent() { return this.receivedRate > 0 ? ((this.paidAmount * this.paidRate) / this.receivedRate).toFixed(2) : '0.00' },
        }">
        @csrf

        <x-ui.legacy-form title="تبادله ارز" :back-route="route('currency-exchanges.index')" save-label="ذخیره">
            <x-ui.field label="تاریخ" name="date" type="date" :value="now()->toDateString()" required />

            <div class="legacy-field">
                <label>صندوق / بانک</label>
                <div class="flex gap-4 text-sm mb-2">
                    <label class="flex items-center gap-1"><input type="radio" name="holder_type" x-model="holderType" value="cashbox" checked> صندوق</label>
                    <label class="flex items-center gap-1"><input type="radio" name="holder_type" x-model="holderType" value="bank"> بانک</label>
                </div>
                <div x-show="holderType === 'cashbox'">
                    <select name="cashbox_id" data-searchable>
                        <option value="">— انتخاب —</option>
                        @foreach($cashboxes as $cashbox)<option value="{{ $cashbox->id }}">{{ $cashbox->name }}</option>@endforeach
                    </select>
                </div>
                <div x-show="holderType === 'bank'" x-cloak>
                    <select name="bank_account_id" data-searchable>
                        <option value="">— انتخاب —</option>
                        @foreach($bankAccounts as $bank)<option value="{{ $bank->id }}">{{ $bank->name }}</option>@endforeach
                    </select>
                </div>
            </div>

            <hr style="border-color:#d7dce1;margin:14px 0">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-ui.field label="مبلغ پرداختی" name="paid_amount" type="number" step="0.01" required x-model.number="paidAmount" />
                <x-ui.field label="ارز پرداختی" name="paid_currency_id" type="select" required :options="$currencies->pluck('code', 'id')->all()" />
                <x-ui.field label="نرخ ارز پرداختی" name="paid_rate" type="number" step="0.0001" :value="old('paid_rate', 1)" required x-model.number="paidRate" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-ui.field label="مبلغ دریافتی" name="received_amount" type="number" step="0.01" required />
                <x-ui.field label="ارز دریافتی" name="received_currency_id" type="select" required :options="$currencies->pluck('code', 'id')->all()" />
                <x-ui.field label="نرخ ارز دریافتی" name="received_rate" type="number" step="0.0001" :value="old('received_rate', 1)" required x-model.number="receivedRate" />
            </div>

            <div class="legacy-field">
                <label>معادل (تخمینی، به ارز دریافتی)</label>
                <input type="text" readonly x-bind:value="equivalent" style="background:#f2f4f6;color:#586470;text-align:left">
            </div>

            <x-ui.field label="توضیحات" name="description" type="textarea" />
        </x-ui.legacy-form>
    </form>
</x-layouts.app>
