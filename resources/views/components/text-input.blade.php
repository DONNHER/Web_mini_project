@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-gray-700 border-gray-600 text-white focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm']) }}>
