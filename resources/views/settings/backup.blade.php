<x-layouts.app title="بک آپ اطلاعات">
    <x-ui.page-header title="بک آپ اطلاعات" />

    <div class="bg-white rounded border border-gray-300 p-6 max-w-2xl">
        <div class="mb-6">
            <p class="text-sm text-gray-500 mb-2">یک نسخه از فایل بانک اطلاعاتی جاری را دانلود کنید.</p>
            <a href="{{ route('backup.download') }}" class="inline-block px-4 py-2 bg-sky-700 text-white rounded-md text-sm hover:bg-sky-800">
                دانلود پشتیبان
            </a>
        </div>
        <div class="border-t pt-6">
            <p class="text-sm text-gray-500 mb-2">
                بازیابی اطلاعات، بانک اطلاعاتی جاری را با فایل انتخابی جایگزین می‌کند
                (قبل از جایگزینی یک نسخه پشتیبان خودکار نگهداری می‌شود).
            </p>
            <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data"
                onsubmit="return confirm('اطلاعات فعلی با فایل انتخابی جایگزین می‌شود. ادامه می‌دهید؟')"
                class="flex flex-wrap gap-2 items-center text-sm">
                @csrf
                <input type="file" name="backup" accept=".sqlite,.db" required class="border rounded px-2 py-1.5">
                <button class="px-4 py-2 bg-rose-700 text-white rounded-md text-sm hover:bg-rose-800">بازیابی</button>
            </form>
            @error('backup')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>
</x-layouts.app>
