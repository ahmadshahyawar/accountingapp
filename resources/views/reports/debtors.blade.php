<x-layouts.app title="لیست طلبکار ها">
    <x-ui.page-header title="لیست طلبکار ها (مشتریانی که به ما مقروض اند)" />

    @include('reports._nav')

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm text-right">
            <thead class="bg-gray-50 text-gray-600"><tr><th class="px-4 py-2">مشتری</th><th>مانده طلب</th></tr></thead>
            <tbody class="divide-y">
                @foreach($rows as $row)
                    <tr>
                        <td class="px-4 py-2">{{ $row['person']->name }}</td>
                        <td class="px-4 py-2 font-semibold {{ $row['balance'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ number_format($row['balance'], 2) }}</td>
                    </tr>
                @endforeach
                @if($rows->isEmpty())
                    <tr><td colspan="2" class="px-4 py-6 text-center text-gray-400">مانده ای موجود نیست.</td></tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="font-bold bg-gray-50"><td class="px-4 py-2">مجموع</td><td class="px-4 py-2">{{ number_format($rows->sum('balance'), 2) }}</td></tr>
            </tfoot>
        </table>
    </div>
</x-layouts.app>
