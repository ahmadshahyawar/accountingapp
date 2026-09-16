<x-layouts.app title="انتقال حساب">
    <form action="{{ route('account-transfers.store') }}" method="POST">
        @csrf

        <x-ui.legacy-form title="انتقال حساب" :back-route="route('account-transfers.index')" save-label="ذخیره">
            <x-ui.field label="تاریخ" name="date" type="date" :value="now()->toDateString()" required />

            <hr style="border-color:#d7dce1;margin:14px 0">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6">
                <div>
                    <x-ui.field label="از حساب" name="from_account_id" type="select" required :options="$accounts->pluck('name', 'id')->all()" />
                    <x-ui.field label="شخص مرتبط (اختیاری)" name="from_person_id" type="select" :options="['' => '— هیچکدام —'] + $persons->pluck('name', 'id')->all()" />
                </div>
                <div>
                    <x-ui.field label="به حساب" name="to_account_id" type="select" required :options="$accounts->pluck('name', 'id')->all()" />
                    <x-ui.field label="شخص مرتبط (اختیاری)" name="to_person_id" type="select" :options="['' => '— هیچکدام —'] + $persons->pluck('name', 'id')->all()" />
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
