@props(['active'])

@php
    $tabs = [
        'persons' => ['label' => 'اشخاص', 'route' => route('persons.index')],
        'employees' => ['label' => 'کارمندان', 'route' => route('persons.index', ['type' => 'employee'])],
        'expense' => ['label' => 'مصارف', 'route' => route('accounts.index', ['type' => 'expense'])],
        'revenue' => ['label' => 'عواید', 'route' => route('accounts.index', ['type' => 'revenue'])],
    ];
@endphp

<div class="flex justify-end gap-1 mb-2 text-sm">
    @foreach($tabs as $key => $tab)
        <a href="{{ $tab['route'] }}"
            class="px-4 py-1.5 border-t border-x rounded-t {{ $active === $key ? 'bg-white border-gray-300 font-semibold text-sky-800' : 'bg-gray-100 border-transparent text-gray-500 hover:text-sky-700' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
