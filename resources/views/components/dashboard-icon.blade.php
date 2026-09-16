@props(['name'])

{{-- These are the OLD APP'S ACTUAL icon artwork, not a redrawn approximation —
     cropped directly out of a live screenshot of UnicAccounting.exe (still
     running on this machine) after direct resource extraction turned out to
     be a dead end: the app's embedded resources are encrypted by whatever
     .NET protector it was built with, so there was no way to pull the raw
     images out of the assembly. Each crop already includes the tile's own
     background color baked in, and that color exactly matches this app's
     .tile background (sampled earlier), so there's no visible seam. --}}
<img src="{{ asset('images/dashboard/'.$name.'.png') }}" alt=""
    {{ $attributes->merge(['class' => 'block mx-auto h-24 sm:h-28 w-auto max-w-full object-contain']) }}>
