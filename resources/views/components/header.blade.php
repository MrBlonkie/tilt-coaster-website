<header class="bg-[var(--color-primary)] border-b border-gray-800 font-sans">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="mx-auto flex h-16 max-w-screen-xl items-center gap-8 px-4 sm:px-6 lg:px-8">
        {{-- Logo --}}
        <a href="{{ url('/') }}" class="flex items-center gap-2 shrink-0 group">
            <svg class="h-6 w-6 text-[var(--color-ember)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
            </svg>
            <span class="font-bold text-base tracking-wide text-white">
                <span class="text-[var(--color-ember)]">TILT</span>COASTER
            </span>
        </a>

        <div class="flex flex-1 items-center justify-end md:justify-between">
            {{-- Desktop nav --}}
            <nav aria-label="Global" class="hidden md:block">
                <ul class="flex items-center gap-1 text-sm">
                    <li>
                        <a href="{{ url('/') }}" class="px-4 py-2 rounded-md font-medium transition-all duration-150
                            {{ request()->is('/') ? 'bg-[var(--color-ember)] text-white' : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/manual-control') }}" class="px-4 py-2 rounded-md font-medium transition-all duration-150
                            {{ request()->is('manual-control') ? 'bg-[var(--color-ember)] text-white' : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
                            Manual Control
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/auto-control') }}" class="px-4 py-2 rounded-md font-medium transition-all duration-150
                            {{ request()->is('auto-control') ? 'bg-[var(--color-ember)] text-white' : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
                            Auto Control
                        </a>
                    </li>
                </ul>
            </nav>

            {{-- Mobile hamburger --}}
            <button id="mobile-menu-toggle"
                class="flex items-center justify-center rounded-md p-2 text-gray-400 hover:text-white hover:bg-white/10 transition-colors md:hidden">
                <span class="sr-only">Toggle menu</span>
                <svg id="icon-menu" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg id="icon-close" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-800">
        <nav class="px-4 py-3 space-y-1">
            <a href="{{ url('/') }}" class="flex items-center px-4 py-2.5 rounded-md text-sm font-medium transition-colors
                {{ request()->is('/') ? 'bg-[var(--color-ember)] text-white' : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
                Dashboard
            </a>
            <a href="{{ url('/manual-control') }}" class="flex items-center px-4 py-2.5 rounded-md text-sm font-medium transition-colors
                {{ request()->is('manual-control') ? 'bg-[var(--color-ember)] text-white' : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
                Manual Control
            </a>
            <a href="{{ url('/auto-control') }}" class="flex items-center px-4 py-2.5 rounded-md text-sm font-medium transition-colors
                {{ request()->is('auto-control') ? 'bg-[var(--color-ember)] text-white' : 'text-gray-400 hover:text-white hover:bg-white/10' }}">
                Auto Control
            </a>
        </nav>
    </div>
</header>

<script>
    const _toggle = document.getElementById('mobile-menu-toggle');
    const _mobileMenu = document.getElementById('mobile-menu');
    const _iconMenu = document.getElementById('icon-menu');
    const _iconClose = document.getElementById('icon-close');

    _toggle?.addEventListener('click', () => {
        const isOpen = !_mobileMenu.classList.contains('hidden');
        _mobileMenu.classList.toggle('hidden', isOpen);
        _iconMenu.classList.toggle('hidden', !isOpen);
        _iconClose.classList.toggle('hidden', isOpen);
    });
</script>
