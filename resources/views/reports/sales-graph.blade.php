<x-layouts.app title="گراف فروش">
    <x-ui.page-header title="گراف اجناس فروش (فروش ماهانه)" />

    @include('reports._nav')

    <div class="bg-white rounded-lg shadow p-6">
        @php $max = $monthly->max('total') ?: 1; @endphp
        <div class="flex items-end gap-4 h-64">
            @forelse($monthly as $row)
                <div class="flex flex-col items-center justify-end h-full flex-1">
                    <div class="text-xs text-gray-500 mb-1">{{ number_format($row->total, 0) }}</div>
                    <div class="w-full bg-sky-600 rounded-t" style="height: {{ max(4, ($row->total / $max) * 100) }}%"></div>
                    <div class="text-xs text-gray-500 mt-2">{{ $row->month }}</div>
                </div>
            @empty
                <p class="text-gray-400 text-sm">هنوز فاکتور فروشی ثبت نشده است.</p>
            @endforelse
        </div>
    </div>
</x-layouts.app>
