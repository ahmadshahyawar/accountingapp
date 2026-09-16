<x-layouts.app title="سال مالی">
    <div x-data="{ adding: false }">
        <x-ui.legacy-list title="سال های مالی" create-inline table-id="fy-grid">
            <table class="legacy-grid" id="fy-grid">
                <thead>
                    <tr>
                        <th>نام</th>
                        <th>شروع</th>
                        <th>ختم</th>
                        <th>وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fiscalYears as $fy)
                        <tr data-row>
                            <td>{{ $fy->name }}</td>
                            <td>{{ shamsi($fy->start_date) }}</td>
                            <td>{{ shamsi($fy->end_date) }}</td>
                            <td class="{{ $fy->is_current ? 'text-emerald-700 font-semibold' : '' }}">
                                @if($fy->is_current)
                                    جاری
                                @else
                                    <form action="{{ route('settings.fiscal-years.activate', $fy) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button class="text-sky-700 hover:underline" style="font-size:12px">فعال‌سازی</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if($fiscalYears->isEmpty())
                        <tr><td colspan="4" style="text-align:center;color:#9aa3ab;padding:20px">سال مالی تعریف نشده است.</td></tr>
                    @endif
                </tbody>
            </table>

            <form action="{{ route('settings.fiscal-years.store') }}" method="POST" x-show="adding" x-cloak class="legacy-searchrow" style="margin-top:10px;gap:8px">
                @csrf
                <input name="name" placeholder="نام (مثلاً 1406)" required style="width:120px;border:none;background:transparent;padding:4px 8px;font-size:13px">
                <input type="date" name="start_date" required style="border:1px solid #d1d5db;border-radius:4px;padding:4px 8px;font-size:13px">
                <input type="date" name="end_date" required style="border:1px solid #d1d5db;border-radius:4px;padding:4px 8px;font-size:13px">
                <button class="btn3d" type="submit" style="min-height:30px">افزودن</button>
            </form>
        </x-ui.legacy-list>
    </div>
</x-layouts.app>
