@props(['label', 'name', 'type' => 'text', 'value' => null, 'options' => null, 'required' => false, 'step' => null])

<div class="legacy-field">
    <label for="{{ $name }}">
        {{ $label }} @if($required)<span class="text-red-600">*</span>@endif
    </label>

    @if($type === 'select')
        <select name="{{ $name }}" id="{{ $name }}" data-searchable
            @if($required) required @endif>
            @foreach($options as $optValue => $optLabel)
                <option value="{{ $optValue }}" @selected(old($name, $value) == $optValue)>{{ $optLabel }}</option>
            @endforeach
        </select>
    @elseif($type === 'checkbox')
        <input type="checkbox" name="{{ $name }}" id="{{ $name }}" value="1"
            style="width:16px;height:16px"
            @checked(old($name, $value))>
    @elseif($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $name }}" rows="3">{{ old($name, $value) }}</textarea>
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}"
            @if($step) step="{{ $step }}" @endif
            @if($required) required @endif>
    @endif

    @error($name)
        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
