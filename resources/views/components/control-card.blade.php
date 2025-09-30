<article class="overflow-hidden rounded-lg border border-gray-100 bg-white shadow-xs p-4 sm:p-6 {{ $class ?? '' }}">
    
    <!-- LED Status -->
    @isset($showStatus)
    <div class="flex items-center gap-2 mb-4">
        <div id="{{ $statusId ?? 'led-status' }}" class="w-6 h-6 rounded-full bg-gray-400"></div>
        <div id="{{ $statusTextId ?? 'led-status-text' }}" class="text-sm text-gray-700">laden...</div>
    </div>
    @endisset

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
