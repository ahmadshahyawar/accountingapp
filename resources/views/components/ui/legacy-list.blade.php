@props(['title', 'createRoute' => null, 'createLabel' => 'جدید', 'createInline' => false, 'backRoute' => null, 'tableId' => 'legacy-grid'])

<div class="legacy-list-title">{{ $title }}</div>
<hr style="border-color:#d7dce1;margin-bottom:10px">

{{ $subtabs ?? '' }}

<div class="legacy-searchrow">
    <select><option>همه</option></select>
    <input type="text" placeholder="جستجو" data-table-filter="{{ $tableId }}">
</div>

<div style="overflow-x:auto">
    {{ $slot }}
</div>

<x-ui.grid-toolbar :create-route="$createRoute" :create-label="$createLabel" :create-inline="$createInline" :back-route="$backRoute ?? route('dashboard')" />
