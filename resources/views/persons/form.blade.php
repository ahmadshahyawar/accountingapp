<x-layouts.app title="شخص">
    <x-ui.page-header :title="$person->exists ? 'ویرایش شخص' : 'شخص جدید'" :back-route="route('persons.index')" />

    <form action="{{ $person->exists ? route('persons.update', $person) : route('persons.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 max-w-xl">
        @csrf
        @if($person->exists) @method('PUT') @endif

        @if($person->exists)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">کد</label>
                <input type="text" value="{{ $person->code() }}" disabled
                    class="w-full rounded-md border-gray-300 bg-gray-100 text-gray-500 shadow-sm text-sm py-2 px-3 border">
            </div>
        @endif

        <x-ui.field label="نام" name="name" :value="$person->name" required />
        <x-ui.field label="موبایل" name="mobile" :value="$person->mobile" />
        <x-ui.field label="تلفن" name="phone" :value="$person->phone" />
        <x-ui.field label="آدرس" name="address" :value="$person->address" />

        <div class="mb-4 flex gap-6 text-sm">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_customer" value="1" @checked(old('is_customer', $person->is_customer)) class="rounded border-gray-300 text-sky-600">
                مشتری
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_supplier" value="1" @checked(old('is_supplier', $person->is_supplier)) class="rounded border-gray-300 text-sky-600">
                تامین‌کننده
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_employee" value="1" @checked(old('is_employee', $person->is_employee)) class="rounded border-gray-300 text-sky-600">
                کارمند
            </label>
        </div>

        <x-ui.field label="یادداشت" name="notes" type="textarea" :value="$person->notes" />

        <button type="submit" class="px-6 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">ذخیره</button>
    </form>
</x-layouts.app>
