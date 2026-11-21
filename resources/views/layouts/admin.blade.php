<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ChevalSleep') }} — Command Center</title>

    <!-- Anti-FOUC Theme Init -->
    <script>
        if (localStorage.getItem("chevalsleep_theme") === "light") {
            document.documentElement.classList.remove("dark");
        } else {
            document.documentElement.classList.add("dark");
        }
    </script>

    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-sail-50 dark:bg-navy-900 text-slate-800 dark:text-slate-100 min-h-screen flex">

    <!-- =============================== -->
    <!-- SIDEBAR                         -->
    <!-- =============================== -->
    <aside x-data="{ open: false }"
           @toggle-sidebar.window="open = !open"
           @keydown.window.escape="open = false"
           class="relative z-30">

        <!-- Mobile Overlay -->
        <div x-show="open" x-cloak @click="open = false"
             class="fixed inset-0 bg-navy-950/60 backdrop-blur-sm lg:hidden"></div>

        <!-- Sidebar Panel -->
        <div :class="open ? 'translate-x-0' : '-translate-x-full'"
             class="fixed inset-y-0 left-0 w-60 bg-white dark:bg-navy-800 border-r border-sail-200 dark:border-navy-700 flex flex-col transition-transform duration-150 ease-out lg:translate-x-0 lg:static lg:flex">

            <!-- Brand Header -->
            <div class="p-4 border-b border-sail-200 dark:border-navy-700 flex items-center space-x-2.5 flex-shrink-0">
                <div class="w-8 h-8 rounded-md bg-navy-900 dark:bg-navy-750 border border-brass-500/50 flex items-center justify-center flex-shrink-0 text-brass-400">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center space-x-1.5 leading-none">
                        <span class="font-black text-sm text-navy-950 dark:text-white">CHEVAL<span class="text-brass-500">SLEEP</span></span>
                        <span class="text-[8px] font-mono font-bold px-1 py-0.2 rounded bg-brass-500/15 text-brass-600 dark:text-brass-400 border border-brass-500/30 uppercase">CMS</span>
                    </div>
                    <span class="text-[9px] font-mono text-slate-400 tracking-wider uppercase block mt-0.5">Command Center</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto p-3 space-y-1 text-xs">

                <!-- Overview -->
                <div class="pt-1 pb-1">
                    <span class="text-[9px] uppercase tracking-widest text-slate-400 dark:text-slate-500 px-2.5 font-bold font-mono">OVERVIEW</span>
                </div>

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md transition-colors
                          {{ request()->routeIs('admin.dashboard') 
                             ? 'bg-sail-100 dark:bg-navy-700 text-navy-950 dark:text-brass-400 font-bold border border-sail-200 dark:border-navy-600' 
                             : 'text-slate-600 dark:text-slate-400 hover:text-navy-950 dark:hover:text-white hover:bg-sail-100/60 dark:hover:bg-navy-750' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h3a2 2 0 012 2v3a2 2 0 01-2 2H5a2 2 0 01-2-2V7zM13 7a2 2 0 012-2h2a2 2 0 012 2v8a2 2 0 01-2 2h-2a2 2 0 01-2-2V7zM3 17a2 2 0 012-2h3a2 2 0 012 2v0a2 2 0 01-2 2H5a2 2 0 01-2-2v0z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- CM Events -->
                <div class="pt-3 pb-1">
                    <span class="text-[9px] uppercase tracking-widest text-slate-400 dark:text-slate-500 px-2.5 font-bold font-mono">CHAMPIONS MEETING</span>
                </div>

                <a href="{{ route('admin.events.index') }}"
                   class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md transition-colors
                          {{ request()->routeIs('admin.events.*') 
                             ? 'bg-sail-100 dark:bg-navy-700 text-navy-950 dark:text-brass-400 font-bold border border-sail-200 dark:border-navy-600' 
                             : 'text-slate-600 dark:text-slate-400 hover:text-navy-950 dark:hover:text-white hover:bg-sail-100/60 dark:hover:bg-navy-750' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>CM Editions & Guides</span>
                </a>

                <!-- Master Database -->
                <div class="pt-3 pb-1">
                    <span class="text-[9px] uppercase tracking-widest text-slate-400 dark:text-slate-500 px-2.5 font-bold font-mono">MASTER DATABASE</span>
                </div>

                <a href="{{ route('admin.characters.index') }}"
                   class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md transition-colors
                          {{ request()->routeIs('admin.characters.*') 
                             ? 'bg-sail-100 dark:bg-navy-700 text-navy-950 dark:text-brass-400 font-bold border border-sail-200 dark:border-navy-600' 
                             : 'text-slate-600 dark:text-slate-400 hover:text-navy-950 dark:hover:text-white hover:bg-sail-100/60 dark:hover:bg-navy-750' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Uma Characters</span>
                </a>

                <a href="{{ route('admin.skills.index') }}"
                   class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md transition-colors
                          {{ request()->routeIs('admin.skills.*') 
                             ? 'bg-sail-100 dark:bg-navy-700 text-navy-950 dark:text-brass-400 font-bold border border-sail-200 dark:border-navy-600' 
                             : 'text-slate-600 dark:text-slate-400 hover:text-navy-950 dark:hover:text-white hover:bg-sail-100/60 dark:hover:bg-navy-750' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    <span>Skills Database</span>
                </a>

                <a href="{{ route('admin.support-cards.index') }}"
                   class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md transition-colors
                          {{ request()->routeIs('admin.support-cards.*') 
                             ? 'bg-sail-100 dark:bg-navy-700 text-navy-950 dark:text-brass-400 font-bold border border-sail-200 dark:border-navy-600' 
                             : 'text-slate-600 dark:text-slate-400 hover:text-navy-950 dark:hover:text-white hover:bg-sail-100/60 dark:hover:bg-navy-750' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Support Cards</span>
                </a>

                <!-- Quick Shortcuts -->
                <div class="pt-3 pb-1">
                    <span class="text-[9px] uppercase tracking-widest text-slate-400 dark:text-slate-500 px-2.5 font-bold font-mono">QUICK ACTION</span>
                </div>

                @php $activeEvent = \App\Models\Event::where('is_active', true)->first(); @endphp
                @if ($activeEvent)
                    <a href="{{ route('admin.events.tiers.index', $activeEvent) }}"
                       class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md transition-colors text-brass-600 dark:text-brass-400 bg-brass-500/10 hover:bg-brass-500/20 border border-brass-500/30">
                        <svg class="w-4 h-4 flex-shrink-0 text-brass-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <div class="min-w-0">
                            <div class="font-bold">Active Tier Builder</div>
                            <div class="text-[9px] text-slate-400 truncate">{{ $activeEvent->title }}</div>
                        </div>
                    </a>
                @endif

                <a href="{{ route('home') }}" target="_blank"
                   class="flex items-center space-x-2.5 px-2.5 py-2 rounded-md text-slate-500 hover:text-navy-950 dark:hover:text-white hover:bg-sail-100/60 dark:hover:bg-navy-750 transition-colors">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>Live Portal ↗</span>
                </a>
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-3 border-t border-sail-200 dark:border-navy-700 flex-shrink-0 text-xs font-mono">
                <div class="flex items-center space-x-2.5">
                    <div class="w-7 h-7 rounded bg-navy-900 border border-brass-500/40 flex items-center justify-center font-bold text-xs text-brass-400 flex-shrink-0">
                        {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-navy-950 dark:text-slate-200 truncate">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <div class="text-[9px] text-slate-400 truncate">{{ Auth::user()->email ?? '' }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Log Out"
                                class="p-1 rounded text-slate-400 hover:text-rose-500 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- =============================== -->
    <!-- MAIN CONTENT AREA               -->
    <!-- =============================== -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">

        <!-- Sticky Header Bar -->
        <header class="bg-white/95 dark:bg-navy-800/95 border-b border-sail-200 dark:border-navy-700 sticky top-0 z-20 backdrop-blur-md flex-shrink-0">
            <div class="px-4 sm:px-6 lg:px-8 h-12 flex items-center justify-between gap-4">

                <!-- Left: Hamburger + Page Title -->
                <div class="flex items-center space-x-3">
                    <button @click="$dispatch('toggle-sidebar')" type="button"
                            class="lg:hidden p-1.5 rounded text-slate-500 hover:text-navy-950 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="text-xs font-mono font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                        @yield('page-title', 'Admin Command Center')
                    </div>
                </div>

                <!-- Right: Theme Toggle -->
                <div class="flex items-center space-x-2">
                    <button x-data="{
                        dark: document.documentElement.classList.contains('dark'),
                        toggle() {
                            this.dark = !this.dark;
                            if (this.dark) {
                                document.documentElement.classList.add('dark');
                                localStorage.setItem('chevalsleep_theme', 'dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                                localStorage.setItem('chevalsleep_theme', 'light');
                            }
                        }
                    }" @click="toggle()" type="button"
                            class="p-1.5 rounded text-slate-500 hover:text-navy-950 dark:hover:text-brass-400 transition-colors"
                            title="Toggle Theme">
                        <svg x-show="dark" class="w-4 h-4 text-brass-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="!dark" x-cloak class="w-4 h-4 text-navy-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-5">

            <!-- Toast Notifications -->
            @if (session('status') || session('success'))
                <div x-data="{ show: true }" x-show="show" x-cloak
                     x-init="setTimeout(() => show = false, 4000)"
                     class="fixed bottom-5 right-5 z-50 max-w-sm bg-white dark:bg-navy-800 border border-emerald-500/50 rounded-md shadow-lg px-4 py-3 flex items-center space-x-2.5 text-xs font-mono">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="flex-1 text-navy-950 dark:text-slate-100">{{ session('status') ?? session('success') }}</span>
                    <button @click="show = false" class="text-slate-400 hover:text-navy-950 dark:hover:text-white">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-cloak
                     class="fixed bottom-5 right-5 z-50 max-w-sm bg-white dark:bg-navy-800 border border-rose-500/50 rounded-md shadow-lg px-4 py-3 flex items-center space-x-2.5 text-xs font-mono">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span class="flex-1 text-rose-600 dark:text-rose-400">{{ session('error') }}</span>
                    <button @click="show = false" class="text-slate-400 hover:text-navy-950 dark:hover:text-white">&times;</button>
                </div>
            @endif

            <!-- Validation Errors -->
            @if (isset($errors) && $errors->any())
                <div x-data="{ show: true }" x-show="show" x-cloak
                     class="mb-5 bg-rose-50 dark:bg-rose-950/30 border border-rose-300 dark:border-rose-900/60 rounded-md p-3 text-xs font-mono text-rose-700 dark:text-rose-300">
                    <div class="flex items-center justify-between font-bold mb-1">
                        <span>Validation Errors:</span>
                        <button @click="show = false" class="text-rose-500 hover:text-rose-700">&times;</button>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="border-t border-sail-200 dark:border-navy-700 py-3 text-center text-[10px] font-mono text-slate-400 flex-shrink-0">
            ChevalSleep Protocol &bull; Uma Musume Global Competitive Meta Operations
        </footer>
    </div>

</body>
</html>
