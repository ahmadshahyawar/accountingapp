<x-layouts.app title="انتقال پول">
    <x-ui.page-header title="انتقال پول جدید" :back-route="route('money-transfers.index')" />

    <form action="{{ route('money-transfers.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 max-w-2xl"
        x-data="{ fromType: 'cashbox', toType: 'cashbox' }">
        @csrf

        <x-ui.field label="تاریخ" name="date" type="date" :value="now()->toDateString()" required />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 border-t pt-4 mt-2">
            <div>
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">از صندوق / بانک</label>
                    <div class="flex gap-4 text-sm">
                        <label class="flex items-center gap-1"><input type="radio" name="from_type" x-model="fromType" value="cashbox" checked> صندوق</label>
                        <label class="flex items-center gap-1"><input type="radio" name="from_type" x-model="fromType" value="bank"> بانک</label>
                    </div>
                </div>
                <div x-show="fromType === 'cashbox'">
                    <x-ui.field label="صندوق" name="from_cashbox_id" type="select" :options="['' => '— انتخاب —'] + $cashboxes->pluck('name', 'id')->all()" />
                </div>
                <div x-show="fromType === 'bank'" x-cloak>
                    <x-ui.field label="حساب بانکی" name="from_bank_account_id" type="select" :options="['' => '— انتخاب —'] + $bankAccounts->pluck('name', 'id')->all()" />
                </div>
            </div>

            <div>
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">به صندوق / بانک</label>
                    <div class="flex gap-4 text-sm">
                        <label class="flex items-center gap-1"><input type="radio" name="to_type" x-model="toType" value="cashbox" checked> صندوق</label>
                        <label class="flex items-center gap-1"><input type="radio" name="to_type" x-model="toType" value="bank"> بانک</label>
                    </div>
                </div>
                <div x-show="toType === 'cashbox'">
                    <x-ui.field label="صندوق" name="to_cashbox_id" type="select" :options="['' => '— انتخاب —'] + $cashboxes->pluck('name', 'id')->all()" />
                </div>
                <div x-show="toType === 'bank'" x-cloak>
                    <x-ui.field label="حساب بانکی" name="to_bank_account_id" type="select" :options="['' => '— انتخاب —'] + $bankAccounts->pluck('name', 'id')->all()" />
                </div>
            </div>
        </div>

        <x-ui.field label="مبلغ" name="amount" type="number" step="0.01" required />

        <div class="grid grid-cols-2 gap-4">
            <x-ui.field label="واحد پول" name="currency_id" type="select" required :options="$currencies->pluck('code', 'id')->all()" />
            <x-ui.field label="نرخ تبدیل به ارز پایه" name="fx_rate" type="number" step="0.0001" value="1" required />
        </div>

        <x-ui.field label="توضیحات" name="description" type="textarea" />

        <button type="submit" class="px-6 py-2 bg-sky-700 hover:bg-sky-800 text-white rounded-md text-sm">ثبت انتقال</button>
    </form>
</x-layouts.app>
