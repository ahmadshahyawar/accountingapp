<x-layouts.app title="انتقال حساب">
    <x-ui.page-header title="انتقال حساب جدید" :back-route="route('account-transfers.index')" />

    <form action="{{ route('account-transfers.store') }}" method="POST" class="bg-white rounded border border-gray-300 p-6 max-w-2xl">
        @csrf

        <x-ui.field label="تاریخ" name="date" type="date" :value="now()->toDateString()" required />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 border-t pt-4 mt-2">
            <div>
                <x-ui.field label="از حساب" name="from_account_id" type="select" required :options="$accounts->pluck('name', 'id')->all()" />
                <x-ui.field label="شخص مرتبط (اختیاری)" name="from_person_id" type="select" :options="['' => '— هیچکدام —'] + $persons->pluck('name', 'id')->all()" />
            </div>
            <div>
                <x-ui.field label="به حساب" name="to_account_id" type="select" required :options="$accounts->pluck('name', 'id')->all()" />
                <x-ui.field label="شخص مرتبط (اختیاری)" name="to_person_id" type="select" :options="['' => '— هیچکدام —'] + $persons->pluck('name', 'id')->all()" />
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
