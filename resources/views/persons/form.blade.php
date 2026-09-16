<x-layouts.app title="شخص">
    <form action="{{ $person->exists ? route('persons.update', $person) : route('persons.store') }}" method="POST">
        @csrf
        @if($person->exists) @method('PUT') @endif

        <x-ui.legacy-form :title="$person->exists ? 'ویرایش شخص' : 'حساب جدید'" :back-route="route('persons.index')">
            @if($person->exists)
                <div class="legacy-field">
                    <label>کد</label>
                    <input type="text" value="{{ $person->code() }}" disabled style="background:#f2f4f6;color:#8a95a1">
                </div>
            @endif

            <x-ui.field label="نام" name="name" :value="$person->name" required />
            <x-ui.field label="نام پدر" name="father_name" :value="$person->father_name" />
            <x-ui.field label="موبایل" name="mobile" :value="$person->mobile" />
            <x-ui.field label="تلفن" name="phone" :value="$person->phone" />
            <x-ui.field label="آدرس" name="address" :value="$person->address" />

            <div class="legacy-field flex gap-6 text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_customer" value="1" @checked(old('is_customer', $person->is_customer))>
                    مشتری
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_supplier" value="1" @checked(old('is_supplier', $person->is_supplier))>
                    تامین‌کننده
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_employee" value="1" @checked(old('is_employee', $person->is_employee))>
                    کارمند
                </label>
            </div>

            <x-ui.field label="یادداشت" name="notes" type="textarea" :value="$person->notes" />
        </x-ui.legacy-form>
    </form>
</x-layouts.app>
