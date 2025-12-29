<!-- Accept a parameter named value -->
@props(['value'])
<!-- Merge the default class with user class -->
<label {{ $attributes->merge(['class' => 'block font-medium text-xl text-gray-700']) }}>
    <!-- if value passed, use it, otherwise use slot -->
    {{ $value ?? $slot }}
</label>
