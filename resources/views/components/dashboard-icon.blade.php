@props(['name'])

{{-- Purpose-built multi-color flat icons for the dashboard tiles specifically
     (unlike the single-emoji ribbon icons) — these tiles are large enough that
     a flat monochrome glyph reads as noticeably cheaper than the old app's
     actual colorful icon art, so it's worth the extra detail here. --}}
<span {{ $attributes->merge(['class' => 'inline-block']) }}>
@switch($name)
    @case('graph')
        <svg viewBox="0 0 64 64" width="56" height="56"><rect x="8" y="34" width="9" height="22" rx="1.5" fill="#F9D65B"/><rect x="20" y="22" width="9" height="34" rx="1.5" fill="#5BC0F8"/><rect x="32" y="10" width="9" height="46" rx="1.5" fill="#8BD450"/><rect x="44" y="26" width="9" height="30" rx="1.5" fill="#F98B5B"/></svg>
        @break
    @case('stock')
        <svg viewBox="0 0 64 64" width="56" height="56"><rect x="16" y="24" width="34" height="28" rx="2" fill="#D9A066"/><rect x="16" y="24" width="34" height="8" fill="#B97E4B"/><path d="M16 32 L33 20 L50 32" fill="none" stroke="#8A5A2B" stroke-width="3" stroke-linejoin="round"/><circle cx="27" cy="16" r="7" fill="#F5C28B"/><rect x="21" y="21" width="12" height="10" rx="3" fill="#4A6FA5"/></svg>
        @break
    @case('pay-out')
        <svg viewBox="0 0 64 64" width="56" height="56"><rect x="14" y="24" width="34" height="20" rx="2" fill="#7CC24B" transform="rotate(-8 31 34)"/><circle cx="31" cy="34" r="6" fill="#FFF3C4" transform="rotate(-8 31 34)"/><path d="M40 46 Q48 40 54 30 Q58 24 52 20 Q47 17 42 24 L34 34" fill="#F4C99B" stroke="#D9A876" stroke-width="1.5"/></svg>
        @break
    @case('pay-in')
        <svg viewBox="0 0 64 64" width="56" height="56"><rect x="16" y="24" width="34" height="20" rx="2" fill="#7CC24B" transform="rotate(8 33 34)"/><circle cx="33" cy="34" r="6" fill="#FFF3C4" transform="rotate(8 33 34)"/><path d="M24 46 Q16 40 10 30 Q6 24 12 20 Q17 17 22 24 L30 34" fill="#F4C99B" stroke="#D9A876" stroke-width="1.5"/></svg>
        @break
    @case('cart')
        <svg viewBox="0 0 64 64" width="56" height="56"><circle cx="24" cy="52" r="4.5" fill="#2C3E50"/><circle cx="44" cy="52" r="4.5" fill="#2C3E50"/><path d="M10 14h6l6 30h28l6-22H20" fill="none" stroke="#EAF6FF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @break
    @case('basket')
        <svg viewBox="0 0 64 64" width="56" height="56"><path d="M14 26h36l-4 24a4 4 0 01-4 3.5H22a4 4 0 01-4-3.5L14 26z" fill="#8BD450"/><path d="M22 26l4-12M42 26l-4-12" stroke="#5B9B2B" stroke-width="3" fill="none" stroke-linecap="round"/><rect x="12" y="22" width="40" height="6" rx="2" fill="#5B9B2B"/></svg>
        @break
    @case('person-money')
        <svg viewBox="0 0 64 64" width="56" height="56"><circle cx="28" cy="20" r="10" fill="#F4C99B"/><path d="M12 52c0-10 8-16 16-16s16 6 16 16" fill="#4A6FA5"/><circle cx="46" cy="46" r="12" fill="#7CC24B"/><text x="46" y="51" font-size="14" fill="white" text-anchor="middle" font-family="Arial" font-weight="bold">$</text></svg>
        @break
    @case('documents')
        <svg viewBox="0 0 64 64" width="56" height="56"><rect x="18" y="10" width="28" height="36" rx="2" fill="#EAF6FF" stroke="#5BA3D0" stroke-width="2"/><rect x="12" y="18" width="28" height="36" rx="2" fill="#FFFFFF" stroke="#2E86C1" stroke-width="2"/><line x1="17" y1="27" x2="35" y2="27" stroke="#2E86C1" stroke-width="2"/><line x1="17" y1="34" x2="35" y2="34" stroke="#2E86C1" stroke-width="2"/><line x1="17" y1="41" x2="29" y2="41" stroke="#2E86C1" stroke-width="2"/></svg>
        @break
    @case('ledger')
        <svg viewBox="0 0 64 64" width="56" height="56"><rect x="14" y="12" width="36" height="44" rx="3" fill="#FFFFFF" stroke="#00889E" stroke-width="2.5"/><rect x="24" y="8" width="16" height="10" rx="2" fill="#00889E"/><line x1="20" y1="28" x2="44" y2="28" stroke="#00889E" stroke-width="2.5"/><line x1="20" y1="36" x2="44" y2="36" stroke="#00889E" stroke-width="2.5"/><line x1="20" y1="44" x2="36" y2="44" stroke="#00889E" stroke-width="2.5"/></svg>
        @break
    @case('trend')
        <svg viewBox="0 0 64 64" width="48" height="48"><path d="M10 44 L24 30 L34 38 L54 16" fill="none" stroke="#FFFFFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/><path d="M42 16h12v12" fill="none" stroke="#FFFFFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
        @break
    @case('id-card')
        <svg viewBox="0 0 64 64" width="56" height="56"><rect x="8" y="16" width="48" height="32" rx="3" fill="#FFFFFF" stroke="#1D9D51" stroke-width="2.5"/><circle cx="22" cy="32" r="6" fill="#1D9D51"/><line x1="34" y1="27" x2="48" y2="27" stroke="#1D9D51" stroke-width="2.5"/><line x1="34" y1="34" x2="48" y2="34" stroke="#1D9D51" stroke-width="2.5"/><line x1="14" y1="42" x2="30" y2="42" stroke="#1D9D51" stroke-width="2.5"/></svg>
        @break
    @case('box')
        <svg viewBox="0 0 64 64" width="56" height="56"><path d="M32 8 L54 18 V46 L32 56 L10 46 V18 Z" fill="#FFFFFF" fill-opacity="0.15"/><path d="M32 8 L54 18 L32 28 L10 18 Z" fill="#FFFFFF"/><path d="M10 18 L32 28 V56 L10 46 Z" fill="#FFFFFF" fill-opacity="0.7"/><path d="M54 18 L32 28 V56 L54 46 Z" fill="#FFFFFF" fill-opacity="0.5"/></svg>
        @break
    @default
        <svg viewBox="0 0 64 64" width="56" height="56"><rect x="14" y="14" width="36" height="36" rx="4" fill="#FFFFFF" fill-opacity="0.3"/></svg>
@endswitch
</span>
