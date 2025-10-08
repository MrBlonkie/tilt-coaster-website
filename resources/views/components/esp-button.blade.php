<button
    type="button"
    {{ $attributes->merge(['class' => '
        relative inline-block px-8 py-3 text-sm font-medium text-[var(--color-secondary)] 
        bg-[var(--color-accent)] border-[var(--color-wood)] 
        transition-all duration-200 ease-in-out
        hover:bg-[var(--color-highlight)] hover:text-[var(--color-secondary)] 
        focus:ring-3 focus:ring-[var(--color-wood)] focus:outline-none
    ']) }}
>
    {{ $slot }}
</button>