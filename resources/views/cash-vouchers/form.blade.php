<x-layouts.app title="رسید نقدی">
    <x-ui.page-header :title="$type === 'receipt' ? 'رسید دریافت نقدی' : 'رسید پرداخت نقدی'" :back-route="route('cash-vouchers.index')" />

    <form action="{{ route('cash-vouchers.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 max-w-2xl" x-data="{ source: 'cashbox', contraType: 'person' }">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">

        <x-ui.field label="تاریخ" name="date" type="date" :value="now()->toDateString()" required />

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">منبع وجه</label>
            <div class="flex gap-4 text-sm">
                <label class="flex items-center gap-1"><input type="radio" x-model="source" value="cashbox" checked> صندوق نقدی</label>
                <label class="flex items-center gap-1"><input type="radio" x-model="source" value="bank"> بانک</label>
            </div>
        </div>

        <div x-show="source === 'cashbox'">
            <x-ui.field label="صندوق" name="cashbox_id" type="select"
                :options="['' => '— انتخاب —'] + $cashboxes->pluck('name', 'id')->all()" />
        </div>
        <div x-show="source === 'bank'" x-cloak>
            <x-ui.field label="حساب بانکی" name="bank_account_id" type="select"
                :options="['' => '— انتخاب —'] + $bankAccounts->pluck('name', 'id')->all()" />
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">طرف مقابل</label>
            <div class="flex gap-4 text-sm">
                <label class="flex items-center gap-1"><input type="radio" x-model="contraType" value="person" checked> شخص (مشتری/تامین‌کننده)</label>
                <label class="flex items-center gap-1"><input type="radio" x-model="contraType" value="account"> حساب {{ $type === 'receipt' ? 'عواید' : 'مصرف' }}</label>
            </div>
        </div>

        <div x-show="contraType === 'person'">
            <x-ui.field label="شخص" name="person_id" type="select"
                :options="['' => '— انتخاب —'] + $persons->pluck('name', 'id')->all()" />
        </div>
        <div x-show="contraType === 'account'" x-cloak>
            <x-ui.field label="حساب" name="contra_account_id" type="select"
                :options="['' => '— انتخاب —'] + ($type === 'receipt' ? $revenueAccounts : $expenseAccounts)->pluck('name', 'id')->all()" />
        </div>

        <x-ui.field label="مبلغ" name="amount" type="number" step="0.01" required />

        <div class="grid grid-cols-2 gap-4">
            <x-ui.field label="واحد پول" name="currency_id" type="select" required
                :options="$currencies->pluck('code', 'id')->all()" />
            <x-ui.field label="نرخ تبدیل به ارز پایه" name="fx_rate" type="number" step="0.0001" value="1" required />
        </div>

        <x-ui.field label="توضیحات" name="description" type="textarea" />

        <button type="submit" class="px-6 py-2 {{ $type === 'receipt' ? 'bg-green-700 hover:bg-green-800' : 'bg-pink-700 hover:bg-pink-800' }} text-white rounded-md text-sm">
            ثبت رسید
        </button>
    </form>
</x-layouts.app>
