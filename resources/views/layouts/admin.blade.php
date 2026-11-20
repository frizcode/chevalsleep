<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ChevalSleep') }} — Admin CMS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-950 text-slate-100 min-h-screen flex flex-col">
    <!-- Top Admin Navigation -->
    <header class="bg-slate-900/90 border-b border-slate-800 sticky top-0 z-40 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500 to-rose-600 flex items-center justify-center shadow-lg shadow-rose-950/40 text-slate-950 font-black text-lg group-hover:scale-105 transition-transform">
                        CS
                    </div>
                    <div>
                        <span class="font-extrabold text-lg tracking-tight bg-gradient-to-r from-slate-100 to-slate-400 bg-clip-text text-transparent">ChevalSleep</span>
                        <span class="text-[10px] uppercase font-bold tracking-widest text-amber-400 block -mt-1">Admin CMS</span>
                    </div>
                </a>

                <nav class="hidden md:flex items-center space-x-1 pl-4 border-l border-slate-800">
                    <a href="{{ route('admin.dashboard') }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-amber-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.events.index') }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('admin.events.*') ? 'bg-slate-800 text-amber-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Events & Tournaments
                    </a>
                    <a href="{{ route('admin.characters.index') }}"
                       class="px-3.5 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request()->routeIs('admin.characters.*') ? 'bg-slate-800 text-amber-400' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Characters
                    </a>
                </nav>
            </div>

            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" target="_blank"
                   class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white border border-slate-700/60 transition-colors">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>View Public Site</span>
                </a>

                <!-- User Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" type="button" class="flex items-center space-x-2 text-sm text-slate-300 hover:text-white focus:outline-none">
                        <span class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs text-amber-400">
                            {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                        </span>
                        <span class="hidden sm:inline font-medium text-xs">{{ Auth::user()->name ?? 'Admin' }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 mt-2 w-48 bg-slate-900 border border-slate-800 rounded-xl shadow-xl py-1 z-50 text-sm">
                        <div class="px-4 py-2 border-b border-slate-800/80 text-xs text-slate-400">
                            Signed in as <strong class="text-slate-200 block truncate">{{ Auth::user()->email ?? '' }}</strong>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-slate-300 hover:bg-slate-800 hover:text-white">Profile</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-rose-400 hover:bg-slate-800">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Alert -->
        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" class="mb-6 bg-emerald-950/60 border border-emerald-500/40 text-emerald-300 px-4 py-3 rounded-xl flex items-center justify-between text-sm">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-400 hover:text-emerald-200">&times;</button>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div x-data="{ show: true }" x-show="show" class="mb-6 bg-rose-950/60 border border-rose-500/40 text-rose-300 px-4 py-3 rounded-xl text-sm">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2 font-bold">
                        <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Please correct the errors below:</span>
                    </div>
                    <button @click="show = false" class="text-rose-400 hover:text-rose-200">&times;</button>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="border-t border-slate-800/80 py-6 text-center text-xs text-slate-500">
        ChevalSleep Uma Musume Global Meta Admin CMS &bull; Built with Laravel & Tailwind CSS
    </footer>
</body>
</html>
