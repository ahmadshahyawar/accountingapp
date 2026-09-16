@props(['createRoute' => null, 'createLabel' => 'جدید', 'createInline' => false, 'backRoute' => null, 'editLabel' => 'ویرایش', 'deleteLabel' => 'حذف', 'deleteConfirm' => 'حذف شود؟', 'printLabel' => 'چاپ', 'reportOnly' => false])

<div class="grid-toolbar" data-grid-toolbar>
    @unless($reportOnly)
    <div class="grp">
        @if($createInline)
            <button type="button" class="btn3d" @click="adding = !adding">
                <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="8"/><path d="M10 6v8M6 10h8"/></svg>
                {{ $createLabel }}
            </button>
        @elseif($createRoute)
            <a href="{{ $createRoute }}" class="btn3d">
                <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="8"/><path d="M10 6v8M6 10h8"/></svg>
                {{ $createLabel }}
            </a>
        @endif
        <button type="button" class="btn3d" data-role="edit" disabled>
            <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M13.5 3.5l3 3L6 17H3v-3L13.5 3.5z"/></svg>
            {{ $editLabel }}
        </button>
        <button type="button" class="btn3d" data-role="delete" disabled data-confirm="{{ $deleteConfirm }}">
            <svg class="ic ic-red" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="9"/><path d="M7 7l6 6M13 7l-6 6"/></svg>
            {{ $deleteLabel }}
        </button>
        {{ $slot }}
    </div>
    @endunless
    <div class="grp">
        <button type="button" class="btn3d" onclick="window.print()">
            <svg class="ic ic-gray" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 8V3h10v5M5 14h10v3H5v-3zM3 8h14v5h-3M3 8v4h2"/></svg>
            {{ $printLabel }}
        </button>
        @if($backRoute)
            <a href="{{ $backRoute }}" class="btn3d">
                <svg class="ic ic-red" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="9"/><path d="M7 7l6 6M13 7l-6 6"/></svg>
                خروج
            </a>
        @endif
    </div>
</div>
