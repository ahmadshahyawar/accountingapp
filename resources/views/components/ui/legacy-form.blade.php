@props(['title', 'backRoute', 'saveLabel' => 'ذخیره'])

<div class="legacy-form-title">{{ $title }}</div>
<hr style="border-color:#d7dce1;margin-bottom:14px">

{{ $slot }}

<div class="grid-toolbar">
    <div class="grp">
        <button type="submit" class="btn3d">
            <svg class="ic ic-blue" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 4h10l2 2v10H4V4z"/><path d="M6 4v4h6V4M6 12h8"/></svg>
            {{ $saveLabel }}
        </button>
    </div>
    <div class="grp">
        <a href="{{ $backRoute }}" class="btn3d">
            <svg class="ic ic-red" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="10" cy="10" r="9"/><path d="M7 7l6 6M13 7l-6 6"/></svg>
            انصراف
        </a>
    </div>
</div>
