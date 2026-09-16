<x-layouts.app title="انتقال پول">
    <form action="{{ route('money-transfers.store') }}" method="POST" x-data="{ fromType: 'cashbox', toType: 'cashbox' }">
        @csrf

        <x-ui.legacy-form title="انتقال پول" :back-route="route('money-transfers.index')" save-label="ذخیره">
            <x-ui.field label="تاریخ" name="date" type="date" :value="now()->toDateString()" required />

            <hr style="border-color:#d7dce1;margin:14px 0">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6">
                <div>
                    <div class="legacy-field" style="margin-bottom:6px">
                        <label>از صندوق / بانک</label>
                    </div>
                    <div class="flex gap-4 text-sm mb-2">
                        <label class="flex items-center gap-1"><input type="radio" name="from_type" x-model="fromType" value="cashbox" checked> صندوق</label>
                        <label class="flex items-center gap-1"><input type="radio" name="from_type" x-model="fromType" value="bank"> بانک</label>
                    </div>
                    <div x-show="fromType === 'cashbox'">
                        <select name="from_cashbox_id" data-searchable>
                            <option value="">— انتخاب —</option>
                            @foreach($cashboxes as $cashbox)<option value="{{ $cashbox->id }}">{{ $cashbox->name }}</option>@endforeach
                        </select>
                    </div>
                    <div x-show="fromType === 'bank'" x-cloak>
                        <select name="from_bank_account_id" data-searchable>
                            <option value="">— انتخاب —</option>
                            @foreach($bankAccounts as $bank)<option value="{{ $bank->id }}">{{ $bank->name }}</option>@endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <div class="legacy-field" style="margin-bottom:6px">
                        <label>به صندوق / بانک</label>
                    </div>
                    <div class="flex gap-4 text-sm mb-2">
                        <label class="flex items-center gap-1"><input type="radio" name="to_type" x-model="toType" value="cashbox" checked> صندوق</label>
                        <label class="flex items-center gap-1"><input type="radio" name="to_type" x-model="toType" value="bank"> بانک</label>
                    </div>
                    <div x-show="toType === 'cashbox'">
                        <select name="to_cashbox_id" data-searchable>
                            <option value="">— انتخاب —</option>
                            @foreach($cashboxes as $cashbox)<option value="{{ $cashbox->id }}">{{ $cashbox->name }}</option>@endforeach
                        </select>
                    </div>
                    <div x-show="toType === 'bank'" x-cloak>
                        <select name="to_bank_account_id" data-searchable>
                            <option value="">— انتخاب —</option>
                            @foreach($bankAccounts as $bank)<option value="{{ $bank->id }}">{{ $bank->name }}</option>@endforeach
                        </select>
                    </div>
                </div>
            </div>

            <hr style="border-color:#d7dce1;margin:14px 0">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-ui.field label="مبلغ" name="amount" type="number" step="0.01" required />
                <x-ui.field label="ارز" name="currency_id" type="select" required :options="$currencies->pluck('code', 'id')->all()" />
                <x-ui.field label="نرخ ارز" name="fx_rate" type="number" step="0.0001" :value="old('fx_rate', 1)" required />
            </div>

            <x-ui.field label="توضیحات" name="description" type="textarea" />
        </x-ui.legacy-form>
    </form>
</x-layouts.app>
