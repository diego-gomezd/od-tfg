@props(['disabled' => false])

<textarea {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-md sm:text-sm border-gray-300']) !!}>
{{ $slot }}
</textarea>