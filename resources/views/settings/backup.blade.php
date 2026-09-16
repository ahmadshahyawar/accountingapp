<x-layouts.app title="بک آپ اطلاعات">
    <div class="legacy-form-title">بک آپ اطلاعات</div>
    <hr style="border-color:#d7dce1;margin-bottom:14px">

    <div class="mb-6">
        <p class="text-sm text-gray-500 mb-2">یک نسخه از فایل بانک اطلاعاتی جاری را دانلود کنید.</p>
        <a href="{{ route('backup.download') }}" class="btn3d">
            <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M10 3v10M6 9l4 4 4-4M4 16h12"/></svg>
            دانلود پشتیبان
        </a>
    </div>
    <hr style="border-color:#d7dce1;margin:16px 0">
    <div>
        <p class="text-sm text-gray-500 mb-3">
            بازیابی اطلاعات، بانک اطلاعاتی جاری را با فایل انتخابی جایگزین می‌کند
            (قبل از جایگزینی یک نسخه پشتیبان خودکار نگهداری می‌شود).
        </p>
        <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data"
            onsubmit="return confirm('اطلاعات فعلی با فایل انتخابی جایگزین می‌شود. ادامه می‌دهید؟')"
            class="flex flex-wrap gap-3 items-center text-sm">
            @csrf
            <input type="file" name="backup" accept=".sqlite,.db" required style="border:1px solid #b9bfc6;border-radius:3px;padding:6px 8px;font-size:13px">
            <button type="submit" class="btn3d">
                <svg class="ic ic-red" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 10a6 6 0 1 1 2 4.5M4 10v4H0"/></svg>
                بازیابی
            </button>
        </form>
        @error('backup')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
</x-layouts.app>
