<x-layouts.app title="تغییر رمز عبور">
    <x-ui.page-header title="تغییر رمز عبور" />

    <form action="{{ route('profile.password.update') }}" method="POST" class="bg-white rounded-lg shadow p-6 max-w-md">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">رمز عبور فعلی<span class="text-red-600">*</span></label>
            <input type="password" name="current_password" required
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border">
            @error('current_password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">رمز عبور جدید<span class="text-red-600">*</span></label>
            <input type="password" name="password" required
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border">
            @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">تکرار رمز عبور جدید<span class="text-red-600">*</span></label>
            <input type="password" name="password_confirmation" required
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border">
        </div>

        <button type="submit" class="px-6 py-2 bg-sky-700 hover:bg-sky-800 text-white rounded-md text-sm">ذخیره</button>
    </form>
</x-layouts.app>
