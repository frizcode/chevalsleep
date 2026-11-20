<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ChevalSleep') }} — Uma Musume Global Competitive Meta Tier List</title>

    <!-- Anti-FOUC Theme Initialization (Admiral Dark default) -->
    <script>
        if (localStorage.getItem("chevalsleep_theme") === "light") {
            document.documentElement.classList.remove("dark");
        } else {
            document.documentElement.classList.add("dark");
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-[#090E1A] text-slate-900 dark:text-slate-100 min-h-screen flex flex-col selection:bg-amber-500 selection:text-slate-950 transition-colors duration-200">

    <!-- Subtle Background Ambient Glows (Dark Mode Only) -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10 hidden dark:block">
        <div class="absolute -top-40 left-1/4 w-96 h-96 bg-amber-500/10 rounded-full blur-[140px]"></div>
        <div class="absolute top-1/3 -right-20 w-[500px] h-[500px] bg-sky-600/10 rounded-full blur-[160px]"></div>
        <div class="absolute -bottom-20 left-1/3 w-[600px] h-[600px] bg-indigo-600/10 rounded-full blur-[180px]"></div>
    </div>

    <!-- Top Navigation Bar -->
    <header class="bg-white/80 dark:bg-[#090E1A]/85 border-b border-sky-100 dark:border-sky-900/40 sticky top-0 z-40 backdrop-blur-xl transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <!-- Left: Brand Logo & Links -->
            <div class="flex items-center space-x-6 sm:space-x-8">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <!-- Marine Feather Emblem -->
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-sky-600 via-sky-700 to-amber-500 dark:from-sky-700 dark:via-sky-800 dark:to-amber-500 flex items-center justify-center shadow-lg shadow-sky-900/20 text-white font-black text-xl group-hover:scale-105 transition-all duration-300">
                        <svg class="w-6 h-6 text-amber-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center space-x-1.5">
                            <span class="font-black text-xl tracking-tight text-slate-900 dark:text-white">Cheval<span class="text-sky-600 dark:text-sky-400">Sleep</span></span>
                            <span class="text-[9px] uppercase font-black px-1.5 py-0.2 rounded bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30">v2.0</span>
                        </div>
                        <span class="text-[10px] uppercase font-bold tracking-widest text-slate-500 dark:text-slate-400 block -mt-1">Cheval Grand Edition</span>
                    </div>
                </a>

                <!-- Desktop Navigation Menu -->
                <nav class="hidden md:flex items-center space-x-1 pl-2">
                    <a href="{{ route('home') }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('home') ? 'bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                        Home
                    </a>
                    <a href="{{ route('cm.latest') }}"
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('cm.*') ? 'bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                        Champions Meeting
                    </a>

                    <!-- Guides Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 flex items-center space-x-1">
                            <span>Guides & Wiki</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-56 bg-white dark:bg-[#11192E] border border-sky-100 dark:border-sky-900/60 rounded-2xl shadow-xl py-1.5 z-50 overflow-hidden text-xs">
                            <a href="{{ route('guides.umas') }}" class="block px-4 py-2 hover:bg-sky-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 hover:text-sky-700 dark:hover:text-white transition-colors">
                                <div class="font-bold">Uma Database</div>
                                <div class="text-[10px] text-slate-400">All racers, growth & aptitudes</div>
                            </a>
                            <a href="{{ route('guides.supoka') }}" class="block px-4 py-2 hover:bg-sky-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 hover:text-sky-700 dark:hover:text-white transition-colors">
                                <div class="font-bold">Support Cards</div>
                                <div class="text-[10px] text-slate-400">SSR/SR tier list & hints</div>
                            </a>
                            <a href="{{ route('guides.skills') }}" class="block px-4 py-2 hover:bg-sky-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 hover:text-sky-700 dark:hover:text-white transition-colors">
                                <div class="font-bold">Skills & Mechanics</div>
                                <div class="text-[10px] text-slate-400">Acceleration & race math</div>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Right: Theme Toggle & Admin Actions -->
            <div class="flex items-center space-x-3">
                
                <!-- Alpine.js Persistent Theme Toggle (Sun/Moon) -->
                <button type="button"
                        x-data="{
                            isDark: document.documentElement.classList.contains('dark'),
                            toggleTheme() {
                                this.isDark = !this.isDark;
                                if (this.isDark) {
                                    document.documentElement.classList.add('dark');
                                    localStorage.setItem('chevalsleep_theme', 'dark');
                                } else {
                                    document.documentElement.classList.remove('dark');
                                    localStorage.setItem('chevalsleep_theme', 'light');
                                }
                            }
                        }"
                        @click="toggleTheme()"
                        class="p-2 rounded-2xl bg-slate-100 dark:bg-slate-800/80 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-600 dark:text-amber-400 border border-slate-200 dark:border-slate-700 transition-colors shadow-sm"
                        :title="isDark ? 'Switch to Seafoam Porcelain Day' : 'Switch to Admiral Night'">
                    <!-- Sun icon for light mode (shows when dark is active) -->
                    <svg x-show="isDark" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <!-- Moon icon for dark mode (shows when light is active) -->
                    <svg x-show="!isDark" x-cloak class="w-4 h-4 text-sky-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Admin CMS Button -->
                @auth
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded-2xl text-xs font-bold bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 transition-all shadow-md shadow-amber-950/20">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="hidden sm:inline">Admin CMS</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white px-2 py-1 transition-colors">
                        Staff Login
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">
        @yield('content')
    </main>

    <!-- Public Footer -->
    <footer class="border-t border-sky-100 dark:border-sky-900/40 bg-white/60 dark:bg-[#090E1A]/60 py-8 mt-16 backdrop-blur-md transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 dark:text-slate-400">
            <div class="flex items-center space-x-2">
                <span class="font-extrabold text-slate-900 dark:text-white">ChevalSleep</span>
                <span>&bull;</span>
                <span>Cheval Grand Edition Uma Musume Global Meta Guide</span>
            </div>
            <div>
                Crafted for Champions Meeting (CM) & League of Heroes Tournaments
            </div>
        </div>
    </footer>

</body>
</html>
