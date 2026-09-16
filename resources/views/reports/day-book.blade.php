<x-layouts.app title="دفتر روزنامچه">
    <x-ui.page-header title="دفتر روزنامچه (Day Book)" />

    @include('reports._nav')

    <form method="GET" class="bg-white rounded border border-gray-300 p-4 mb-4 flex flex-wrap gap-3 items-end text-sm">
        <div><label class="block text-xs text-gray-500 mb-1">از تاریخ</label><input type="date" name="from" value="{{ $from }}" class="border rounded px-2 py-1"></div>
        <div><label class="block text-xs text-gray-500 mb-1">الی تاریخ</label><input type="date" name="to" value="{{ $to }}" class="border rounded px-2 py-1"></div>
        <button class="px-3 py-1.5 bg-sky-700 text-white rounded">نمایش</button>
    </form>

    <div class="space-y-4">
        @foreach($entries as $entry)
            <div class="bg-white rounded border border-gray-300 p-4">
                <div class="flex justify-between text-sm text-gray-500 mb-2">
                    <span>{{ shamsi($entry->date) }} — {{ $entry->description }}</span>
                    <span class="text-xs">سند #{{ $entry->id }}</span>
                </div>
                <table class="w-full text-sm text-right">
                    <thead class="text-gray-400"><tr><th class="py-1">حساب</th><th>شخص</th><th>مدین</th><th>داین</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($entry->lines as $line)
                            <tr>
                                <td class="py-1">{{ $line->account->code }} - {{ $line->account->name }}</td>
                                <td class="text-gray-500">{{ $line->person?->name }}</td>
                                <td>{{ $line->debit > 0 ? number_format($line->debit, 2) : '' }}</td>
                                <td>{{ $line->credit > 0 ? number_format($line->credit, 2) : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach

        @if($entries->isEmpty())
            <p class="text-gray-500 text-sm">در این بازه تاریخی سندی ثبت نشده است.</p>
        @endif
    </div>
</x-layouts.app>
