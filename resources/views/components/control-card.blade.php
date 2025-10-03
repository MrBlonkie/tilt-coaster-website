<article class="overflow-hidden rounded-lg border border-gray-100 bg-white shadow-xs p-4 sm:p-6 {{ $class ?? '' }}">

    <!-- Slot voor extra content -->
    <div class="mb-4">
        {{ $slot }}
    </div>

    <!-- Knoppen slot -->
    @isset($buttons)
    <div class="flex gap-2">
        {{ $buttons }}
    </div>
    @endisset
</article>
