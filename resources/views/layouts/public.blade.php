<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ChevalSleep') }} — Uma Musume Global Competitive Meta</title>

    <!-- Anti-FOUC Theme Initialization (Dark default) -->
    <script>
        if (localStorage.getItem("chevalsleep_theme") === "light") {
            document.documentElement.classList.remove("dark");
        } else {
            document.documentElement.classList.add("dark");
        }
    </script>

    <!-- Google Fonts: Inter (UI & Headings) and JetBrains Mono (Telemetry) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-sail-50 dark:bg-navy-900 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col selection:bg-brass-500 selection:text-navy-950 transition-colors duration-150">

    <!-- Top Navigation Bar -->
    <header x-data="{ mobileMenuOpen: false }" class="bg-white/95 dark:bg-navy-800/95 border-b border-sail-200 dark:border-navy-700 sticky top-0 z-40 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
            
            <!-- Left: Brand & Desktop Navigation Links -->
            <div class="flex items-center space-x-6 sm:space-x-8">
                <!-- Cheval Grand Naval Emblem Brand -->
                <a href="{{ route('home') }}" class="flex items-center space-x-2.5 group">
                    <div class="w-8 h-8 rounded-md bg-navy-900 dark:bg-navy-750 border border-brass-500/50 flex items-center justify-center flex-shrink-0 text-brass-400 group-hover:border-brass-400 transition-colors shadow-sm">
                        <!-- Naval Star Compass Emblem -->
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center space-x-1.5 leading-none">
                            <span class="font-black tracking-wider text-base text-navy-950 dark:text-white">CHEVAL<span class="text-brass-500 dark:text-brass-400">SLEEP</span></span>
                            <span class="text-[9px] font-mono font-bold px-1 py-0.5 rounded bg-sail-100 dark:bg-navy-700 text-slate-600 dark:text-slate-300 border border-sail-200 dark:border-navy-600 uppercase">META</span>
                        </div>
                        <span class="text-[9px] font-mono tracking-widest text-slate-400 dark:text-slate-500 uppercase block mt-0.5">Uma Musume Global Protocol</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1 pl-4 border-l border-sail-200 dark:border-navy-700">
                    <a href="{{ route('home') }}"
                       class="px-3 py-1.5 rounded-md text-xs font-semibold transition-colors {{ request()->routeIs('home') ? 'bg-sail-100 dark:bg-navy-700 text-navy-950 dark:text-brass-400 font-bold border border-sail-200 dark:border-navy-600' : 'text-slate-600 dark:text-slate-300 hover:text-navy-950 dark:hover:text-white hover:bg-sail-100/60 dark:hover:bg-navy-750' }}">
                        {{ __('ui.nav.home') }}
                    </a>
                    <!-- CM Guides Dropdown (Ongoing Live & Past Archives) -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                                class="px-3 py-1.5 rounded-md text-xs font-semibold flex items-center space-x-1 transition-colors {{ request()->routeIs('cm.*') ? 'bg-sail-100 dark:bg-navy-700 text-navy-950 dark:text-brass-400 font-bold border border-sail-200 dark:border-navy-600' : 'text-slate-600 dark:text-slate-300 hover:text-navy-950 dark:hover:text-white hover:bg-sail-100/60 dark:hover:bg-navy-750' }}">
                            <span>{{ __('ui.nav.cm_guides') }}</span>
                            <svg class="w-3 h-3 text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute left-0 mt-1.5 w-72 bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md shadow-xl py-1 z-50 text-xs">
                            
                            <!-- Section: Ongoing CM (Live) -->
                            <div class="px-3 py-1.5 bg-sail-100/60 dark:bg-navy-900/60 border-b border-sail-200 dark:border-navy-700 flex items-center justify-between">
                                <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex items-center space-x-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>{{ __('ui.cm.ongoing_cm') }}</span>
                                </span>
                            </div>
                            
                            <div class="divide-y divide-sail-100 dark:divide-navy-750">
                                @forelse ($navLiveEvents ?? [] as $live)
                                    <a href="{{ route('cm.show', $live) }}" class="block px-3.5 py-2.5 hover:bg-sail-100 dark:hover:bg-navy-750 transition-colors group">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-navy-950 dark:text-sail-100 group-hover:text-brass-500 transition-colors">{{ $live->title }}</span>
                                            <span class="text-[9px] font-mono px-1 rounded bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-bold uppercase">LIVE</span>
                                        </div>
                                        <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                                            {{ $live->track_name }} &bull; {{ $live->distance_meters }}m ({{ $live->distance_type }}) &bull; {{ $live->surface }}
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-3.5 py-2 text-[11px] text-slate-400 italic">
                                        {{ __('ui.cm.no_ongoing') }}
                                    </div>
                                @endforelse
                            </div>

                            <!-- Section: Past CMs (Archive) -->
                            <div class="px-3 py-1.5 bg-sail-100/60 dark:bg-navy-900/60 border-t border-b border-sail-200 dark:border-navy-700 flex items-center justify-between">
                                <span class="font-mono text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                                    {{ __('ui.cm.past_cm') }}
                                </span>
                            </div>

                            <div class="max-h-56 overflow-y-auto divide-y divide-sail-100 dark:divide-navy-750">
                                @forelse ($navArchivedEvents ?? [] as $past)
                                    <a href="{{ route('cm.show', $past) }}" class="block px-3.5 py-2 hover:bg-sail-100 dark:hover:bg-navy-750 transition-colors group">
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-slate-700 dark:text-slate-200 group-hover:text-brass-500 transition-colors truncate">{{ $past->title }}</span>
                                            <span class="text-[9px] font-mono text-slate-400">{{ $past->distance_type }}</span>
                                        </div>
                                        <div class="text-[10px] font-mono text-slate-400 mt-0.5">
                                            {{ $past->track_name }} &bull; {{ $past->surface }}
                                        </div>
                                    </a>
                                @empty
                                    <div class="px-3.5 py-2 text-[11px] text-slate-400 italic">
                                        No past CMs archived yet.
                                    </div>
                                @endforelse
                            </div>

                            <!-- Footer: All CM Archives -->
                            <div class="p-2 border-t border-sail-200 dark:border-navy-700 bg-sail-50 dark:bg-navy-900/80">
                                <a href="{{ route('cm.archive') }}" class="block text-center text-[11px] font-mono font-bold text-brass-500 hover:text-brass-400 transition-colors">
                                    {{ __('ui.cm.all_archives') }} &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Encyclopedia Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                                class="px-3 py-1.5 rounded-md text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-navy-950 dark:hover:text-white hover:bg-sail-100/60 dark:hover:bg-navy-750 flex items-center space-x-1 transition-colors {{ request()->routeIs('guides.*') ? 'text-cerulean-600 dark:text-cerulean-400' : '' }}">
                            <span>{{ __('ui.nav.encyclopedia') }}</span>
                            <svg class="w-3 h-3 text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute left-0 mt-1.5 w-52 bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md shadow-lg py-1 z-50 text-xs">
                            <a href="{{ route('guides.umas') }}" class="block px-3.5 py-2 hover:bg-sail-100 dark:hover:bg-navy-750 text-slate-700 dark:text-slate-200 transition-colors">
                                <div class="font-bold">{{ __('ui.nav.umas') }}</div>
                                <div class="text-[10px] text-slate-400">{{ __('ui.database.umas_desc') }}</div>
                            </a>
                            <a href="{{ route('guides.supoka') }}" class="block px-3.5 py-2 hover:bg-sail-100 dark:hover:bg-navy-750 text-slate-700 dark:text-slate-200 transition-colors">
                                <div class="font-bold">{{ __('ui.nav.supoka') }}</div>
                                <div class="text-[10px] text-slate-400">{{ __('ui.database.supoka_desc') }}</div>
                            </a>
                            <a href="{{ route('guides.skills') }}" class="block px-3.5 py-2 hover:bg-sail-100 dark:hover:bg-navy-750 text-slate-700 dark:text-slate-200 transition-colors">
                                <div class="font-bold">{{ __('ui.nav.skills') }}</div>
                                <div class="text-[10px] text-slate-400">{{ __('ui.database.skills_desc') }}</div>
                            </a>
                        </div>
                    </div>

                    @auth
                        @if (auth()->user()->isUser())
                            <a href="{{ route('bookmarks.index') }}"
                               class="px-3 py-1.5 rounded-md text-xs font-semibold transition-colors {{ request()->routeIs('bookmarks.*') ? 'bg-sail-100 dark:bg-navy-700 text-navy-950 dark:text-brass-400 font-bold border border-sail-200 dark:border-navy-600' : 'text-slate-600 dark:text-slate-300 hover:text-navy-950 dark:hover:text-white hover:bg-sail-100/60 dark:hover:bg-navy-750' }}">
                                {{ __('ui.nav.my_bookmarks') }}
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>

            <!-- Right Controls: Language Switcher, Theme Toggle & Auth Buttons -->
            <div class="hidden md:flex items-center space-x-2">
                
                <!-- Language Switcher (EN / ID) -->
                <div class="flex items-center bg-sail-100 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md p-0.5 text-[10px] font-mono font-bold">
                    <a href="{{ route('locale.switch', 'en') }}"
                       class="px-2 py-0.5 rounded transition-colors {{ app()->getLocale() === 'en' ? 'bg-white dark:bg-navy-750 text-cerulean-600 dark:text-cerulean-400 shadow-sm' : 'text-slate-400 hover:text-navy-900 dark:hover:text-sail-100' }}">
                        EN
                    </a>
                    <a href="{{ route('locale.switch', 'id') }}"
                       class="px-2 py-0.5 rounded transition-colors {{ app()->getLocale() === 'id' ? 'bg-white dark:bg-navy-750 text-cerulean-600 dark:text-cerulean-400 shadow-sm' : 'text-slate-400 hover:text-navy-900 dark:hover:text-sail-100' }}">
                        ID
                    </a>
                </div>

                <!-- Theme Toggle -->
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
                        class="p-1.5 rounded-md text-slate-500 dark:text-slate-400 hover:text-navy-950 dark:hover:text-brass-400 hover:bg-sail-100 dark:hover:bg-navy-750 border border-transparent hover:border-sail-200 dark:hover:border-navy-700 transition-colors"
                        title="Toggle Light / Dark Mode">
                    <svg x-show="isDark" x-cloak class="w-4 h-4 text-brass-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg x-show="!isDark" x-cloak class="w-4 h-4 text-navy-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Auth Navigation -->
                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-md text-xs font-mono font-bold bg-navy-900 dark:bg-navy-750 text-brass-400 border border-brass-500/40 hover:border-brass-400 transition-colors shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ __('ui.nav.admin_cms') }}</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-2.5 py-1.5 text-xs font-semibold text-slate-500 hover:text-rose-500 transition-colors">
                                {{ __('ui.nav.logout') }}
                            </button>
                        </form>
                    @else
                        <!-- Trainer Dropdown -->
                        <div x-data="{ openUser: false }" class="relative">
                            <button @click="openUser = !openUser" type="button"
                                    class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-md text-xs font-semibold bg-sail-100 dark:bg-navy-750 text-navy-900 dark:text-sail-100 border border-sail-200 dark:border-navy-700 hover:border-cerulean-500 transition-colors">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span class="max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="openUser" @click.away="openUser = false" x-cloak
                                 class="absolute right-0 mt-1.5 w-44 bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md shadow-lg py-1 z-50 text-xs">
                                <a href="{{ route('bookmarks.index') }}" class="block px-3.5 py-2 hover:bg-sail-100 dark:hover:bg-navy-750 text-slate-700 dark:text-slate-200 transition-colors">
                                    {{ __('ui.nav.my_bookmarks') }}
                                </a>
                                <a href="{{ route('profile.edit') }}" class="block px-3.5 py-2 hover:bg-sail-100 dark:hover:bg-navy-750 text-slate-700 dark:text-slate-200 transition-colors">
                                    {{ __('ui.nav.profile') }}
                                </a>
                                <div class="border-t border-sail-200 dark:border-navy-700 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3.5 py-2 hover:bg-rose-50 dark:hover:bg-rose-950/30 text-rose-600 dark:text-rose-400 transition-colors">
                                        {{ __('ui.nav.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-md text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-navy-950 dark:hover:text-white transition-colors">
                        {{ __('ui.nav.login') }}
                    </a>
                    <a href="{{ route('register') }}" class="px-3 py-1.5 rounded-md text-xs font-semibold bg-brass-500 hover:bg-brass-400 text-navy-950 transition-colors shadow-sm">
                        {{ __('ui.nav.register') }}
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Toggle -->
            <div class="flex items-center space-x-2 md:hidden">
                <!-- Mobile Theme Toggle -->
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
                        class="p-1.5 rounded-md text-slate-500 dark:text-slate-400">
                    <svg x-show="isDark" x-cloak class="w-4 h-4 text-brass-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <svg x-show="!isDark" x-cloak class="w-4 h-4 text-navy-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                </button>

                <!-- Hamburger Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                        class="p-1.5 rounded-md text-slate-600 dark:text-slate-300 hover:bg-sail-100 dark:hover:bg-navy-750 focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenuOpen" x-cloak
             class="md:hidden border-t border-sail-200 dark:border-navy-700 bg-white dark:bg-navy-800 px-4 pt-3 pb-5 space-y-3 shadow-xl">
            <div class="flex items-center justify-between pb-2 border-b border-sail-200 dark:border-navy-750">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider font-mono">{{ __('ui.nav.language') }}</span>
                <div class="flex items-center bg-sail-100 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md p-0.5 text-xs font-mono font-bold">
                    <a href="{{ route('locale.switch', 'en') }}" class="px-2 py-0.5 rounded {{ app()->getLocale() === 'en' ? 'bg-white dark:bg-navy-750 text-cerulean-600 dark:text-cerulean-400' : 'text-slate-400' }}">EN</a>
                    <a href="{{ route('locale.switch', 'id') }}" class="px-2 py-0.5 rounded {{ app()->getLocale() === 'id' ? 'bg-white dark:bg-navy-750 text-cerulean-600 dark:text-cerulean-400' : 'text-slate-400' }}">ID</a>
                </div>
            </div>

            <nav class="space-y-1 text-sm">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md font-semibold {{ request()->routeIs('home') ? 'bg-sail-100 dark:bg-navy-700 text-brass-500 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                    {{ __('ui.nav.home') }}
                </a>
                <!-- Mobile CM Guides Accordion (Live & Past CMs) -->
                <div x-data="{ cmOpen: false }" class="space-y-1">
                    <button @click="cmOpen = !cmOpen" type="button"
                            class="w-full flex items-center justify-between px-3 py-2 rounded-md font-semibold text-slate-700 dark:text-slate-300 hover:bg-sail-100 dark:hover:bg-navy-750 transition-colors {{ request()->routeIs('cm.*') ? 'bg-sail-100 dark:bg-navy-700 text-brass-500 font-bold' : '' }}">
                        <span>{{ __('ui.nav.cm_guides') }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': cmOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    
                    <div x-show="cmOpen" x-cloak class="pl-3 pr-1 py-1 space-y-2 border-l-2 border-brass-500/40 ml-3">
                        <div class="text-[10px] font-mono font-bold uppercase text-emerald-600 dark:text-emerald-400 tracking-wider flex items-center space-x-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>{{ __('ui.cm.ongoing_cm') }}</span>
                        </div>
                        @forelse ($navLiveEvents ?? [] as $live)
                            <a href="{{ route('cm.show', $live) }}" class="block px-2 py-1 text-xs font-bold text-navy-950 dark:text-sail-100 hover:text-brass-500">
                                {{ $live->title }} ({{ $live->distance_meters }}m)
                            </a>
                        @empty
                            <div class="text-[11px] text-slate-400 italic px-2">{{ __('ui.cm.no_ongoing') }}</div>
                        @endforelse

                        <div class="text-[10px] font-mono font-bold uppercase text-slate-400 tracking-wider pt-1.5">
                            {{ __('ui.cm.past_cm') }}
                        </div>
                        @forelse ($navArchivedEvents ?? [] as $past)
                            <a href="{{ route('cm.show', $past) }}" class="block px-2 py-1 text-xs text-slate-600 dark:text-slate-300 hover:text-brass-500">
                                {{ $past->title }}
                            </a>
                        @empty
                            <div class="text-[11px] text-slate-400 italic px-2">No archives</div>
                        @endforelse

                        <div class="pt-1 border-t border-sail-200 dark:border-navy-700">
                            <a href="{{ route('cm.archive') }}" class="block px-2 py-1 text-xs font-mono font-bold text-brass-500 hover:text-brass-400">
                                {{ __('ui.cm.all_archives') }} &rarr;
                            </a>
                        </div>
                    </div>
                </div>
                <a href="{{ route('guides.umas') }}" class="block px-3 py-2 rounded-md font-semibold text-slate-700 dark:text-slate-300">
                    {{ __('ui.nav.umas') }}
                </a>
                <a href="{{ route('guides.supoka') }}" class="block px-3 py-2 rounded-md font-semibold text-slate-700 dark:text-slate-300">
                    {{ __('ui.nav.supoka') }}
                </a>
                <a href="{{ route('guides.skills') }}" class="block px-3 py-2 rounded-md font-semibold text-slate-700 dark:text-slate-300">
                    {{ __('ui.nav.skills') }}
                </a>
                @auth
                    @if (auth()->user()->isUser())
                        <a href="{{ route('bookmarks.index') }}" class="block px-3 py-2 rounded-md font-semibold text-cerulean-600 dark:text-cerulean-400">
                            {{ __('ui.nav.my_bookmarks') }}
                        </a>
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-md font-semibold text-slate-700 dark:text-slate-300">
                            {{ __('ui.nav.profile') }}
                        </a>
                    @endif
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md font-bold text-brass-500 bg-navy-900/40">
                            {{ __('ui.nav.admin_cms') }}
                        </a>
                    @endif
                @endauth
            </nav>

            <div class="pt-3 border-t border-sail-200 dark:border-navy-750 flex flex-col gap-2">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-center px-4 py-2 rounded-md text-xs font-bold text-rose-500 bg-rose-500/10">
                            {{ __('ui.nav.logout') }}
                        </button>
                    </form>
                @else
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('login') }}" class="text-center px-3 py-2 rounded-md text-xs font-semibold bg-sail-100 dark:bg-navy-700 text-navy-950 dark:text-white">
                            {{ __('ui.nav.login') }}
                        </a>
                        <a href="{{ route('register') }}" class="text-center px-3 py-2 rounded-md text-xs font-semibold bg-brass-500 text-navy-950">
                            {{ __('ui.nav.register') }}
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- Nautical Footer -->
    <footer class="border-t border-sail-200 dark:border-navy-700 bg-white/70 dark:bg-navy-800/60 py-6 mt-16 text-xs text-slate-500 dark:text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center space-x-2">
                <span class="font-black text-navy-900 dark:text-white tracking-wider">CHEVALSLEEP</span>
                <span>&bull;</span>
                <span class="font-mono text-[11px]">Uma Musume Global Competitive Meta Telemetry</span>
            </div>
            <div class="font-mono text-[11px] text-slate-400">
                Maintained for Champions Meeting & League of Heroes
            </div>
        </div>
    </footer>

</body>
</html>
