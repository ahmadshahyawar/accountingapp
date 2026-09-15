@props(['title', 'backRoute' => null])

<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold">{{ $title }}</h2>
    <div class="flex gap-2">
        {{ $slot }}
        @if($backRoute)
            <a href="{{ $backRoute }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300">بازگشت</a>
        @endif
    </div>
</div>
