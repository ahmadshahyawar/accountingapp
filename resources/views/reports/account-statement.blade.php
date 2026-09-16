<x-layouts.app title="گزارش حساب">
    <x-ui.page-header title="گزارش حساب (Account Statement)" />

    @include('reports._nav')

    <form method="GET" class="bg-white rounded border border-gray-300 p-4 mb-4 flex flex-wrap gap-3 items-end text-sm">
        <div>
            <label class="block text-xs text-gray-500 mb-1">حساب</label>
            <select name="account_id" class="border rounded px-2 py-1" required onchange="this.form.submit()">
                <option value="">— انتخاب —</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" @selected($accountId == $acc->id)>{{ $acc->code }} - {{ $acc->name }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="block text-xs text-gray-500 mb-1">از تاریخ</label><input type="date" name="from" value="{{ $from }}" class="border rounded px-2 py-1"></div>
        <div><label class="block text-xs text-gray-500 mb-1">الی تاریخ</label><input type="date" name="to" value="{{ $to }}" class="border rounded px-2 py-1"></div>
        <button class="px-3 py-1.5 bg-sky-700 text-white rounded">نمایش</button>
    </form>

    @if($account)
        <div class="bg-white rounded border border-gray-300 overflow-x-auto">
            <table class="w-full text-sm text-right">
                <thead class="bg-gray-50 text-gray-600">
                    <tr><th class="px-4 py-2">تاریخ</th><th>شرح</th><th>شخص</th><th>مدین</th><th>داین</th><th>مانده</th></tr>
                </thead>
                <tbody class="divide-y">
                    <tr class="bg-gray-50 font-semibold">
                        <td class="px-4 py-2" colspan="5">مانده قبلی</td>
                        <td class="px-4 py-2">{{ number_format($openingBalance, 2) }}</td>
                    </tr>
                    @php $running = $openingBalance; @endphp
                    @foreach($lines as $line)
                        @php
                            $delta = $account->normal_balance === 'debit' ? $line->base_debit - $line->base_credit : $line->base_credit - $line->base_debit;
                            $running += $delta;
                        @endphp
                        <tr>
                            <td class="px-4 py-2">{{ shamsi($line->journalEntry->date) }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $line->description ?? $line->journalEntry->description }}</td>
                            <td class="px-4 py-2 text-gray-500">{{ $line->person?->name }}</td>
                            <td class="px-4 py-2">{{ number_format($line->base_debit, 2) }}</td>
                            <td class="px-4 py-2">{{ number_format($line->base_credit, 2) }}</td>
                            <td class="px-4 py-2 font-semibold">{{ number_format($running, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.app>
