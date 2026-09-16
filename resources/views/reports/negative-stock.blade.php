<x-layouts.app title="اجناس منفی">
    <x-ui.legacy-list title="اجناس منفی" report-only table-id="negative-stock-grid">
        <x-slot:subtabs>
            @include('reports._nav')
        </x-slot:subtabs>

        <table class="legacy-grid" id="negative-stock-grid">
            <thead>
                <tr><th>کد جنس</th><th>نام جنس</th><th>واحد</th><th>موجودی</th></tr>
            </thead>
            <tbody>
                @foreach($items as $row)
                    <tr data-row>
                        <td>{{ $row['item']->code }}</td>
                        <td>{{ $row['item']->name }}</td>
                        <td>{{ $row['item']->unit->name }}</td>
                        <td class="text-rose-700" style="font-weight:700">{{ number_format($row['quantity'], 2) }}</td>
                    </tr>
                @endforeach
                @if($items->isEmpty())
                    <tr><td colspan="4" style="text-align:center;color:#9aa3ab;padding:20px">جنسی با موجودی منفی وجود ندارد.</td></tr>
                @endif
            </tbody>
        </table>
    </x-ui.legacy-list>
</x-layouts.app>
