@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'block font-black uppercase text-[10px] tracking-widest text-black/40']) }}>
    {{ $value ?? $slot }}
    @if($required)
        <span class="text-red-600 ml-1">*</span>
    @endif
</label>
