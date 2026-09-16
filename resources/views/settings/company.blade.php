<x-layouts.app title="مشخصات شرکت">
    <x-ui.page-header title="مشخصات شرکت" />

    <form action="{{ route('settings.company.update') }}" method="POST" class="bg-white rounded border border-gray-300 p-6 max-w-xl">
        @csrf @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">نام شرکت</label>
            <input type="text" name="name" value="{{ old('name', $company->name) }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3 border">
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">تلفن</label>
                <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">موبایل</label>
                <input type="text" name="mobile" value="{{ old('mobile', $company->mobile) }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3 border">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">ایمیل</label>
            <input type="email" name="email" value="{{ old('email', $company->email) }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3 border">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">وبسایت</label>
            <input type="text" name="website" value="{{ old('website', $company->website) }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3 border">
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">آدرس</label>
            <textarea name="address" rows="3" class="w-full rounded-md border-gray-300 shadow-sm text-sm py-2 px-3 border">{{ old('address', $company->address) }}</textarea>
        </div>

        <button type="submit" class="px-6 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">ذخیره</button>
    </form>
</x-layouts.app>
