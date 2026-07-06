@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-[#FEF6F0] !border-none text-[#1A1A1A] focus:ring-4 focus:ring-[#FF6B00]/5 rounded-2xl !px-8 py-4 font-bold placeholder-[#1A1A1A]/20 shadow-sm w-full outline-none']) }}>
