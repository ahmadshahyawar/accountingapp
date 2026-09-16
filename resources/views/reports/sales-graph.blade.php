<x-layouts.app title="گراف فروش">
    <x-ui.legacy-list title="گراف اجناس فروش" report-only :search="false" table-id="sales-graph-grid">
        <x-slot:subtabs>
            @include('reports._nav')
        </x-slot:subtabs>

        <div id="sales-graph-grid" style="border:1px solid #c7d1db;padding:20px">
            @php $max = $monthly->max('total') ?: 1; @endphp
            <div class="flex items-end gap-4 h-64">
                @forelse($monthly as $row)
                    <div class="flex flex-col items-center justify-end h-full flex-1">
                        <div class="text-xs text-gray-500 mb-1">{{ number_format($row->total, 0) }}</div>
                        <div class="w-full rounded-t" style="height: {{ max(4, ($row->total / $max) * 100) }}%;background:#2f8fd0"></div>
                        <div class="text-xs text-gray-500 mt-2">{{ $row->month }}</div>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">هنوز فاکتور فروشی ثبت نشده است.</p>
                @endforelse
            </div>
        </div>
    </x-ui.legacy-list>
</x-layouts.app>
