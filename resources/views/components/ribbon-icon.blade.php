@props(['name'])

@php
    // Where the real icon has been extracted from a live screenshot of the
    // old app (same technique as the dashboard tiles — see
    // <x-dashboard-icon>), render that actual artwork. Everything else
    // still falls back to an emoji approximation until it's been extracted
    // too; extracting the rest requires clicking through the old app's
    // other ribbon tabs, which turned out to be unsafe to automate (a
    // stray click landed on an unrelated window instead of the target app).
    $hasRealIcon = file_exists(public_path("images/ribbon/{$name}.png"));

    $emoji = [
        'cart' => '🛒',
        'box' => '📦',
        'return' => '↩️',
        'document' => '📄',
        'transfer' => '🔀',
        'cash-in' => '💵',
        'cash-out' => '💸',
        'search' => '🔍',
        'exchange' => '💱',
        'bank' => '🏦',
        'archive' => '🗃️',
        'chart' => '📈',
        'clipboard' => '📋',
        'users' => '👥',
        'user-group' => '👨‍👩‍👧',
        'cube' => '📦',
        'scale' => '⚖️',
        'warehouse' => '🏭',
        'calendar' => '📅',
        'currency' => '💲',
        'user-circle' => '👤',
        'key' => '🔑',
        'cloud-down' => '📥',
        'cloud-up' => '📤',
        'bell' => '🔔',
        'pencil' => '📝',
        'phone' => '📞',
        'cog' => '⚙️',
        'building' => '🏢',
        'exclamation' => '⚠️',
        'grid' => '🔲',
    ];
@endphp
@if($hasRealIcon)
    <img src="{{ asset("images/ribbon/{$name}.png") }}" alt="" {{ $attributes->merge(['class' => 'block mx-auto w-9 h-9 object-contain']) }}>
@else
    <span {{ $attributes->merge(['class' => 'block text-3xl leading-none']) }}>{{ $emoji[$name] ?? $emoji['grid'] }}</span>
@endif
