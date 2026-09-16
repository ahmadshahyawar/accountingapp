<x-layouts.app title="گزارش حساب">
    <x-ui.legacy-list title="گزارش حساب" report-only :search="false" table-id="account-statement-grid">
        <x-slot:subtabs>
            @include('reports._nav')
        </x-slot:subtabs>

        <form method="GET" class="legacy-searchrow" style="gap:8px">
            <select name="account_id" required onchange="this.form.submit()" data-searchable style="flex:1">
                <option value="">— انتخاب —</option>
                @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" @selected($accountId == $acc->id)>{{ $acc->code }} - {{ $acc->name }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ $from }}" style="border:1px solid #d1d5db;border-radius:4px;padding:4px 8px;font-size:13px">
            <input type="date" name="to" value="{{ $to }}" style="border:1px solid #d1d5db;border-radius:4px;padding:4px 8px;font-size:13px">
            <button type="submit" class="btn3d" style="min-height:30px">نمایش</button>
        </form>

        @if($account)
            <table class="legacy-grid" id="account-statement-grid">
                <thead>
                    <tr><th>تاریخ</th><th>شرح</th><th>شخص</th><th>مدین</th><th>داین</th><th>مانده</th></tr>
                </thead>
                <tbody>
                    <tr style="background:#f2f4f6;font-weight:700">
                        <td colspan="5">مانده قبلی</td>
                        <td>{{ number_format($openingBalance, 2) }}</td>
                    </tr>
                    @php $running = $openingBalance; @endphp
                    @foreach($lines as $line)
                        @php
                            $delta = $account->normal_balance === 'debit' ? $line->base_debit - $line->base_credit : $line->base_credit - $line->base_debit;
                            $running += $delta;
                        @endphp
                        <tr data-row>
                            <td>{{ shamsi($line->journalEntry->date) }}</td>
                            <td>{{ $line->description ?? $line->journalEntry->description }}</td>
                            <td>{{ $line->person?->name }}</td>
                            <td>{{ number_format($line->base_debit, 2) }}</td>
                            <td>{{ number_format($line->base_credit, 2) }}</td>
                            <td style="font-weight:700">{{ number_format($running, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </x-ui.legacy-list>
</x-layouts.app>
