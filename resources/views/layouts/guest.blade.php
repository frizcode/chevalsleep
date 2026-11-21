<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ChevalSleep') }} — Trainer Portal</title>

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
<body class="font-sans antialiased bg-sail-50 dark:bg-navy-900 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col justify-between p-4 sm:p-6 transition-colors duration-150">

    <!-- Header bar with home link and language switcher -->
    <div class="max-w-md w-full mx-auto flex items-center justify-between py-2">
        <a href="{{ route('home') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-500 hover:text-navy-950 dark:hover:text-sail-100 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            <span>{{ __('ui.nav.home') }}</span>
        </a>

        <div class="flex items-center bg-sail-100 dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-md p-0.5 text-[10px] font-mono font-bold">
            <a href="{{ route('locale.switch', 'en') }}" class="px-2 py-0.5 rounded transition-colors {{ app()->getLocale() === 'en' ? 'bg-white dark:bg-navy-750 text-cerulean-600 dark:text-cerulean-400 shadow-sm' : 'text-slate-400' }}">EN</a>
            <a href="{{ route('locale.switch', 'id') }}" class="px-2 py-0.5 rounded transition-colors {{ app()->getLocale() === 'id' ? 'bg-white dark:bg-navy-750 text-cerulean-600 dark:text-cerulean-400 shadow-sm' : 'text-slate-400' }}">ID</a>
        </div>
    </div>

    <!-- Centered Card Container -->
    <div class="max-w-md w-full mx-auto my-auto space-y-5">
        <!-- Brand Emblem -->
        <div class="text-center space-y-2">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center space-x-2.5">
                <div class="w-10 h-10 rounded-lg bg-navy-900 dark:bg-navy-800 border border-brass-500/50 flex items-center justify-center text-brass-400 shadow-sm">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L14.5 9.5L22 12L14.5 14.5L12 22L9.5 14.5L2 12L9.5 9.5L12 2Z" />
                    </svg>
                </div>
            </a>
            <h1 class="text-xl font-black tracking-wider text-navy-950 dark:text-white">CHEVAL<span class="text-brass-500">SLEEP</span></h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Uma Musume Global Competitive Meta Protocol</p>
        </div>

        <div class="bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg p-6 sm:p-7 shadow-sm">
            {{ $slot }}
        </div>
    </div>

    <!-- Footer -->
    <div class="max-w-md w-full mx-auto text-center py-4 text-[11px] font-mono text-slate-400">
        ChevalSleep &bull; Authentic Cheval Grand Protocol
    </div>

</body>
</html>
