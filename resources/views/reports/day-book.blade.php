<x-layouts.app title="دفتر روزنامچه">
    <x-ui.legacy-list title="دفتر روزنامچه" report-only :search="false" table-id="day-book-grid">
        <x-slot:subtabs>
            @include('reports._nav')
        </x-slot:subtabs>

        <form method="GET" class="legacy-searchrow" style="gap:8px">
            <input type="date" name="from" value="{{ $from }}" style="border:1px solid #d1d5db;border-radius:4px;padding:4px 8px;font-size:13px">
            <input type="date" name="to" value="{{ $to }}" style="border:1px solid #d1d5db;border-radius:4px;padding:4px 8px;font-size:13px">
            <button type="submit" class="btn3d" style="min-height:30px">نمایش</button>
        </form>

        <div id="day-book-grid" style="display:flex;flex-direction:column;gap:10px">
            @foreach($entries as $entry)
                <div style="border:1px solid #c7d1db">
                    <div style="display:flex;justify-content:space-between;padding:6px 10px;background:#f2f4f6;font-size:13px;color:#586470">
                        <span>{{ shamsi($entry->date) }} — {{ $entry->description }}</span>
                        <span>سند #{{ $entry->id }}</span>
                    </div>
                    <table class="legacy-grid" style="border:none">
                        <thead><tr><th>حساب</th><th>شخص</th><th>مدین</th><th>داین</th></tr></thead>
                        <tbody>
                            @foreach($entry->lines as $line)
                                <tr>
                                    <td>{{ $line->account->code }} - {{ $line->account->name }}</td>
                                    <td>{{ $line->person?->name }}</td>
                                    <td>{{ $line->debit > 0 ? number_format($line->debit, 2) : '' }}</td>
                                    <td>{{ $line->credit > 0 ? number_format($line->credit, 2) : '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach

            @if($entries->isEmpty())
                <p style="color:#9aa3ab;font-size:13px">در این بازه تاریخی سندی ثبت نشده است.</p>
            @endif
        </div>
    </x-ui.legacy-list>
</x-layouts.app>
