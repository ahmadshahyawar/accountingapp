@props(['label', 'name', 'type' => 'text', 'value' => null, 'options' => null, 'required' => false, 'step' => null])

<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }} @if($required)<span class="text-red-600">*</span>@endif
    </label>

    @if($type === 'select')
        <select name="{{ $name }}" id="{{ $name }}"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border"
            @if($required) required @endif>
            @foreach($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" @selected(old($name, $value) == $optValue)>{{ $optLabel }}</option>
            @endforeach
        </select>
    @elseif($type === 'checkbox')
        <input type="checkbox" name="{{ $name }}" id="{{ $name }}" value="1"
            class="rounded border-gray-300 text-sky-600 focus:ring-sky-500"
            @checked(old($name, $value))>
    @elseif($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $name }}" rows="3"
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border">{{ old($name, $value) }}</textarea>
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}"
            @if($step) step="{{ $step }}" @endif
            class="w-full rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm py-2 px-3 border"
            @if($required) required @endif>
    @endif

    @error($name)
        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
