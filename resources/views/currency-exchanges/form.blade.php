<x-layouts.app title="تبادله ارز">
    <x-ui.page-header title="تبادله ارز جدید" :back-route="route('currency-exchanges.index')" />

    <form action="{{ route('currency-exchanges.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 max-w-2xl" x-data="{ holderType: 'cashbox' }">
        @csrf

        <x-ui.field label="تاریخ" name="date" type="date" :value="now()->toDateString()" required />

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">صندوق / بانک</label>
            <div class="flex gap-4 text-sm">
                <label class="flex items-center gap-1"><input type="radio" name="holder_type" x-model="holderType" value="cashbox" checked> صندوق</label>
                <label class="flex items-center gap-1"><input type="radio" name="holder_type" x-model="holderType" value="bank"> بانک</label>
            </div>
        </div>
        <div x-show="holderType === 'cashbox'">
            <x-ui.field label="صندوق" name="cashbox_id" type="select" :options="['' => '— انتخاب —'] + $cashboxes->pluck('name', 'id')->all()" />
        </div>
        <div x-show="holderType === 'bank'" x-cloak>
            <x-ui.field label="حساب بانکی" name="bank_account_id" type="select" :options="['' => '— انتخاب —'] + $bankAccounts->pluck('name', 'id')->all()" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 border-t pt-4 mt-2">
            <div>
                <h3 class="font-bold text-sm mb-2">پرداختی</h3>
                <x-ui.field label="ارز پرداختی" name="paid_currency_id" type="select" required :options="$currencies->pluck('code', 'id')->all()" />
                <x-ui.field label="مبلغ پرداختی" name="paid_amount" type="number" step="0.01" required />
                <x-ui.field label="نرخ ارز پرداختی" name="paid_rate" type="number" step="0.0001" value="1" required />
            </div>
            <div>
                <h3 class="font-bold text-sm mb-2">دریافتی</h3>
                <x-ui.field label="ارز دریافتی" name="received_currency_id" type="select" required :options="$currencies->pluck('code', 'id')->all()" />
                <x-ui.field label="مبلغ دریافتی" name="received_amount" type="number" step="0.01" required />
                <x-ui.field label="نرخ ارز دریافتی" name="received_rate" type="number" step="0.0001" value="1" required />
            </div>
        </div>

        <x-ui.field label="توضیحات" name="description" type="textarea" />

        <button type="submit" class="px-6 py-2 bg-sky-700 hover:bg-sky-800 text-white rounded-md text-sm">ثبت تبادله</button>
    </form>
</x-layouts.app>
