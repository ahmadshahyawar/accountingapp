<x-layouts.app title="حساب">
    <x-ui.page-header :title="$account->exists ? 'ویرایش حساب' : 'حساب جدید'" :back-route="route('accounts.index')" />

    <form action="{{ $account->exists ? route('accounts.update', $account) : route('accounts.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 max-w-xl">
        @csrf
        @if($account->exists) @method('PUT') @endif

        <x-ui.field label="کد حساب" name="code" :value="$account->code" required />
        <x-ui.field label="نام حساب" name="name" :value="$account->name" required />

        <x-ui.field label="حساب مادر" name="parent_id" type="select" :value="$account->parent_id"
            :options="['' => '— ندارد —'] + $parents->pluck('name', 'id')->all()" />

        <x-ui.field label="نوعیت حساب" name="type" type="select" :value="$account->type" required
            :options="['asset' => 'دارایی', 'liability' => 'تعهدات', 'equity' => 'سرمایه', 'revenue' => 'عواید', 'expense' => 'مصارف']" />

        <x-ui.field label="طرف مانده عادی" name="normal_balance" type="select" :value="$account->normal_balance" required
            :options="['debit' => 'مدین (Debit)', 'credit' => 'داین (Credit)']" />

        <div class="flex items-center gap-2 mb-4">
            <x-ui.field label="حساب گروپی (عنوان)، بدون ثبت مستقیم" name="is_group" type="checkbox" :value="$account->is_group" />
        </div>

        <button type="submit" class="px-6 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">ذخیره</button>
    </form>
</x-layouts.app>
