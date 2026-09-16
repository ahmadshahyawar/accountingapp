<x-layouts.app title="سال مالی">
    <x-ui.page-header title="سال های مالی" />

    <div class="bg-white rounded border border-gray-300 p-4">
        <table class="w-full text-sm text-right mb-4">
            <thead class="text-gray-500"><tr><th class="py-1">نام</th><th>شروع</th><th>ختم</th><th>وضعیت</th><th></th></tr></thead>
            <tbody class="divide-y">
                @foreach($fiscalYears as $fy)
                    <tr>
                        <td class="py-1">{{ $fy->name }}</td>
                        <td>{{ shamsi($fy->start_date) }}</td>
                        <td>{{ shamsi($fy->end_date) }}</td>
                        <td>@if($fy->is_current)<span class="text-emerald-700 font-semibold">جاری</span>@endif</td>
                        <td>
                            @unless($fy->is_current)
                                <form action="{{ route('settings.fiscal-years.activate', $fy) }}" method="POST">
                                    @csrf
                                    <button class="text-sky-700 hover:underline">فعال‌سازی</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @endforeach
                @if($fiscalYears->isEmpty())
                    <tr><td colspan="5" class="py-6 text-center text-gray-400">سال مالی تعریف نشده است.</td></tr>
                @endif
            </tbody>
        </table>
        <form action="{{ route('settings.fiscal-years.store') }}" method="POST" class="flex flex-wrap gap-2 items-end text-sm border-t pt-4">
            @csrf
            <div><label class="block text-xs text-gray-500 mb-1">نام (مثلاً 1406)</label><input name="name" class="border rounded px-2 py-1 w-24" required></div>
            <div><label class="block text-xs text-gray-500 mb-1">تاریخ شروع</label><input type="date" name="start_date" class="border rounded px-2 py-1" required></div>
            <div><label class="block text-xs text-gray-500 mb-1">تاریخ ختم</label><input type="date" name="end_date" class="border rounded px-2 py-1" required></div>
            <button class="px-3 py-1.5 bg-sky-700 text-white rounded">افزودن</button>
        </form>
    </div>
</x-layouts.app>
