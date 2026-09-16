<x-layouts.app title="مشخصات شرکت">
    <form action="{{ route('settings.company.update') }}" method="POST">
        @csrf @method('PUT')

        <x-ui.legacy-form title="مشخصات شرکت" :back-route="route('dashboard')">
            <x-ui.field label="نام شرکت" name="name" :value="$company->name" />
            <div class="grid grid-cols-2 gap-4">
                <x-ui.field label="تلفن" name="phone" :value="$company->phone" />
                <x-ui.field label="موبایل" name="mobile" :value="$company->mobile" />
            </div>
            <x-ui.field label="ایمیل" name="email" type="email" :value="$company->email" />
            <x-ui.field label="وبسایت" name="website" :value="$company->website" />
            <x-ui.field label="آدرس" name="address" type="textarea" :value="$company->address" />
        </x-ui.legacy-form>
    </form>
</x-layouts.app>
