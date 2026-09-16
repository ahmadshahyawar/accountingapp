@props(['title', 'createRoute' => null, 'createLabel' => 'جدید', 'createInline' => false, 'backRoute' => null, 'tableId' => 'legacy-grid', 'reportOnly' => false, 'search' => true])

<div class="legacy-list-title">{{ $title }}</div>
<hr style="border-color:#d7dce1;margin-bottom:10px">

{{ $subtabs ?? '' }}

@if($search)
<div class="legacy-searchrow">
    <select><option>همه</option></select>
    <input type="text" placeholder="جستجو" data-table-filter="{{ $tableId }}">
</div>
@endif

<div style="overflow-x:auto">
    {{ $slot }}
</div>

<x-ui.grid-toolbar :create-route="$createRoute" :create-label="$createLabel" :create-inline="$createInline" :back-route="$backRoute ?? route('dashboard')" :report-only="$reportOnly" />
