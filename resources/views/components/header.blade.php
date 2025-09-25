<header class="bg-[var(--color-secondary)] dark:bg-[var(--color-primary)] font-sans">
    <div>
        <title>Tilt-Coaster</title>
    </div>

    <div class="mx-auto flex h-16 max-w-screen-xl items-center gap-8 px-4 sm:px-6 lg:px-8">
        <a class="block text-[var(--color-primary)] dark:text-[var(--color-secondary)]" href="#">
            <span class="sr-only">Home</span>
            <!-- svg icon unchanged -->
        </a>

        <div class="flex flex-1 items-center justify-end md:justify-between">
            <nav aria-label="Global" class="hidden md:block">
                <ul class="flex items-center gap-6 text-sm">
                    <li>
                        <a href="{{ url('/') }}" class="transition 
                            {{ request()->is('/') ? 'underline text-[var(--color-accent)]' : 'text-[var(--color-accent)] hover:text-[var(--color-highlight)]' }} 
                            dark:text-[var(--color-secondary)] 
                            dark:hover:text-[var(--color-highlight)]">
                            Home
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/control') }}" class="transition 
                            {{ request()->is('control') ? 'underline text-[var(--color-accent)]' : 'text-[var(--color-accent)] hover:text-[var(--color-highlight)]' }} 
                            dark:text-[var(--color-secondary)] 
                            dark:hover:text-[var(--color-highlight)]">
                            Control
                        </a>
                    </li>



                    <!-- Repeat for other links -->
                </ul>
            </nav>


            <button
                class="block rounded-sm bg-[var(--color-secondary)] p-2.5 text-[var(--color-primary)] transition hover:text-[var(--color-highlight)] md:hidden dark:bg-[var(--color-primary)] dark:text-[var(--color-secondary)] dark:hover:text-[var(--color-highlight)]">
                <span class="sr-only">Toggle menu</span>
                <!-- svg icon unchanged -->
            </button>
        </div>
    </div>
    </div>
</header>