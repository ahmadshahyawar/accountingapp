@props(['name'])

@php
    // The old app's ribbon icons are full-color DevExpress artwork we don't
    // have access to. Emoji are the closest practical substitute — they're
    // natively colorful (unlike a single-color SVG glyph) and need no asset
    // loading, so a button reads as a distinct colorful icon instead of a
    // uniform colored square, closer to the real ribbon's look.
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
<span {{ $attributes }}>{{ $emoji[$name] ?? $emoji['grid'] }}</span>
