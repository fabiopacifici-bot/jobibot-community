@props(['href' => '#', 'active' => false])

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => 'flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors '
       . ($active
           ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium'
           : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-800')
   ]) }}
   wire:navigate>
    {{ $slot }}
</a>