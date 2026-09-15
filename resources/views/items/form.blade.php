<x-layouts.app title="جنس">
    <x-ui.page-header :title="$item->exists ? 'ویرایش جنس' : 'جنس جدید'" :back-route="route('items.index')" />

    <form action="{{ $item->exists ? route('items.update', $item) : route('items.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 max-w-xl">
        @csrf
        @if($item->exists) @method('PUT') @endif

        <x-ui.field label="کد جنس" name="code" :value="$item->code" required />
        <x-ui.field label="نام جنس" name="name" :value="$item->name" required />

        <x-ui.field label="واحد" name="unit_id" type="select" :value="$item->unit_id" required
            :options="$units->pluck('name', 'id')->all()" />

        <x-ui.field label="گدام پیش‌فرض" name="warehouse_id" type="select" :value="$item->warehouse_id"
            :options="['' => '— ندارد —'] + $warehouses->pluck('name', 'id')->all()" />

        <x-ui.field label="قیمت خرید" name="cost_price" type="number" step="0.01" :value="$item->cost_price" required />
        <x-ui.field label="قیمت فروش" name="sale_price" type="number" step="0.01" :value="$item->sale_price" required />
        <x-ui.field label="حد اقل موجودی (هشدار)" name="reorder_level" type="number" step="0.01" :value="$item->reorder_level" />

        <button type="submit" class="px-6 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">ذخیره</button>
    </form>
</x-layouts.app>
