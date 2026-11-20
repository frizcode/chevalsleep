<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ChevalSleep') }} — Admin CMS Command Center</title>

    <!-- Anti-FOUC Theme Init -->
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
<body class="font-sans antialiased bg-slate-100 dark:bg-[#060C18] text-slate-900 dark:text-slate-100 min-h-screen flex">

    <!-- =============================== -->
    <!-- SIDEBAR                         -->
    <!-- =============================== -->
    <aside x-data="{ open: false }"
           @keydown.window.escape="open = false"
           class="relative z-30">

        <!-- Mobile Overlay -->
        <div x-show="open" x-cloak @click="open = false"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

        <!-- Sidebar Panel -->
        <div :class="open ? 'translate-x-0' : '-translate-x-full'"
             class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-[#0B1221] border-r border-slate-200 dark:border-slate-800/80 flex flex-col shadow-2xl transition-transform duration-200 ease-out lg:translate-x-0 lg:static lg:shadow-none lg:flex">

            <!-- Brand Header -->
            <div class="p-5 border-b border-slate-200 dark:border-slate-800/80 flex items-center space-x-3 flex-shrink-0">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-sky-600 to-amber-500 flex items-center justify-center shadow-lg flex-shrink-0">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center space-x-1.5">
                        <span class="font-black text-base tracking-tight text-slate-900 dark:text-white">ChevalSleep</span>
                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-500/40 uppercase">Admin</span>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-400 dark:text-slate-500">Command Center</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto p-3 space-y-1">

                <!-- Overview -->
                <div class="pt-1 pb-1">
                    <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 px-3">Overview</span>
                </div>

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-2xl text-xs font-bold transition-all
                          {{ request()->routeIs('admin.dashboard') 
                             ? 'bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60 shadow-sm' 
                             : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 012-2h3a2 2 0 012 2v3a2 2 0 01-2 2H5a2 2 0 01-2-2V7zM13 7a2 2 0 012-2h2a2 2 0 012 2v8a2 2 0 01-2 2h-2a2 2 0 01-2-2V7zM3 17a2 2 0 012-2h3a2 2 0 012 2v0a2 2 0 01-2 2H5a2 2 0 01-2-2v0z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Meta Management -->
                <div class="pt-3 pb-1">
                    <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 px-3">Meta Management</span>
                </div>

                <a href="{{ route('admin.events.index') }}"
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-2xl text-xs font-bold transition-all
                          {{ request()->routeIs('admin.events.*') 
                             ? 'bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60 shadow-sm' 
                             : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Events &amp; CM Guides</span>
                </a>

                <a href="{{ route('admin.characters.index') }}"
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-2xl text-xs font-bold transition-all
                          {{ request()->routeIs('admin.characters.*') 
                             ? 'bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800/60 shadow-sm' 
                             : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Uma Characters</span>
                </a>

                <!-- Quick Shortcuts -->
                <div class="pt-3 pb-1">
                    <span class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 dark:text-slate-500 px-3">Quick Actions</span>
                </div>

                @php $activeEvent = \App\Models\Event::where('is_active', true)->first(); @endphp
                @if ($activeEvent)
                    <a href="{{ route('admin.events.tiers.index', $activeEvent) }}"
                       class="flex items-center space-x-3 px-3 py-2.5 rounded-2xl text-xs font-bold transition-all text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/30 hover:bg-amber-100 dark:hover:bg-amber-950/50 border border-amber-200 dark:border-amber-900/50">
                        <svg class="w-4 h-4 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <div class="min-w-0">
                            <div>Active Tier Builder</div>
                            <div class="text-[9px] font-normal text-amber-500/80 truncate">{{ $activeEvent->title }}</div>
                        </div>
                    </a>
                @endif

                <a href="{{ route('home') }}" target="_blank"
                   class="flex items-center space-x-3 px-3 py-2.5 rounded-2xl text-xs font-bold transition-all text-slate-500 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/60">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>View Live Site ↗</span>
                </a>
            </nav>

            <!-- Sidebar Footer: User Info + Logout -->
            <div class="p-4 border-t border-slate-200 dark:border-slate-800/80 flex-shrink-0">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-sky-500 to-amber-500 flex items-center justify-center font-black text-xs text-white flex-shrink-0">
                        {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <div class="text-[10px] text-slate-400 dark:text-slate-500 truncate">{{ Auth::user()->email ?? '' }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Log Out"
                                class="p-1.5 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        <!-- Sticky Top Header Bar -->
        <header class="bg-white/90 dark:bg-[#0B1221]/90 border-b border-slate-200 dark:border-slate-800/80 sticky top-0 z-20 backdrop-blur-xl flex-shrink-0">
            <div class="px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between gap-4">

                <!-- Left: Mobile Hamburger + Breadcrumb -->
                <div class="flex items-center space-x-3">
                    <!-- Mobile hamburger trigger -->
                    <button x-data="{}" @click="$dispatch('toggle-sidebar')" type="button"
                            class="lg:hidden p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Page title slot -->
                    <div class="text-sm font-bold text-slate-700 dark:text-slate-300">
                        @yield('page-title', 'Admin CMS')
                    </div>
                </div>

                <!-- Right: Theme Toggle + View Site -->
                <div class="flex items-center space-x-2">

                    <!-- Light/Dark Mode Toggle -->
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
                            class="p-2 rounded-xl text-slate-500 hover:text-slate-900 dark:hover:text-amber-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                            title="Toggle Light/Dark Mode">
                        <svg x-show="dark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="!dark" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <!-- Profile dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                                class="flex items-center space-x-2 text-xs text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <span class="w-7 h-7 rounded-xl bg-gradient-to-tr from-sky-500 to-amber-500 flex items-center justify-center font-black text-xs text-white">
                                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            </span>
                            <span class="hidden sm:inline font-bold">{{ Auth::user()->name ?? 'Admin' }}</span>
                            <svg class="w-3 h-3 text-slate-400 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-48 bg-white dark:bg-[#0B1221] border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl py-1.5 z-50 text-xs overflow-hidden">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors font-semibold">
                                Profile Settings
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors font-bold">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-7">

            <!-- Toast Notifications -->
            @if (session('status') || session('success'))
                <div x-data="{ show: true }" x-show="show" x-cloak
                     x-init="setTimeout(() => show = false, 4000)"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-4"
                     class="fixed bottom-6 right-6 z-50 max-w-sm bg-white dark:bg-[#0F1D32] border border-emerald-200 dark:border-emerald-700/60 rounded-2xl shadow-2xl px-5 py-4 flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold text-slate-900 dark:text-white">Success</div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">{{ session('status') ?? session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-cloak
                     class="fixed bottom-6 right-6 z-50 max-w-sm bg-white dark:bg-[#0F1D32] border border-rose-200 dark:border-rose-700/60 rounded-2xl shadow-2xl px-5 py-4 flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-bold text-rose-600 dark:text-rose-400">Error</div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-0.5">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Validation Errors Banner -->
            @if (isset($errors) && $errors->any())
                <div x-data="{ show: true }" x-show="show" x-cloak
                     class="mb-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-700/60 rounded-2xl px-5 py-4 text-xs">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2 font-bold text-rose-700 dark:text-rose-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Please fix the following errors:</span>
                        </div>
                        <button @click="show = false" class="text-rose-400 hover:text-rose-600 font-bold text-base">&times;</button>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-rose-600 dark:text-rose-300 pl-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="border-t border-slate-200 dark:border-slate-800/80 py-4 text-center text-[11px] text-slate-400 dark:text-slate-600 flex-shrink-0">
            ChevalSleep Command Center &bull; Uma Musume Global Meta
        </footer>
    </div>

</body>
</html>
