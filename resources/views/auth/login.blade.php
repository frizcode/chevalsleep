<x-guest-layout>
    <div class="mb-5 border-b border-sail-200 dark:border-navy-750 pb-3">
        <h2 class="text-base font-bold text-navy-950 dark:text-sail-50">{{ __('ui.auth.welcome_back') }}</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('ui.auth.login_desc') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4 text-xs">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1 font-mono">
                {{ __('ui.auth.email') }} <span class="text-rose-500">*</span>
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="trainer@chevalsleep.test"
                   class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3.5 py-2 text-xs sm:text-sm text-navy-900 dark:text-sail-100 placeholder-slate-400 focus:outline-none focus:border-cerulean-500 dark:focus:border-brass-500 transition-colors">
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1 font-mono">
                {{ __('ui.auth.password') }} <span class="text-rose-500">*</span>
            </label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   placeholder="••••••••"
                   class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3.5 py-2 text-xs sm:text-sm text-navy-900 dark:text-sail-100 placeholder-slate-400 focus:outline-none focus:border-cerulean-500 dark:focus:border-brass-500 transition-colors">
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="w-4 h-4 rounded bg-sail-50 dark:bg-navy-900 border-sail-300 dark:border-navy-700 text-brass-500 focus:ring-brass-500" name="remember">
                <span class="ms-2 text-xs text-slate-600 dark:text-slate-400">{{ __('ui.auth.remember_me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs text-cerulean-600 dark:text-cerulean-400 hover:underline font-mono" href="{{ route('password.request') }}">
                    {{ __('ui.auth.forgot_password') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button type="submit"
                    class="w-full py-2.5 rounded-md text-xs font-bold uppercase tracking-wider bg-brass-500 hover:bg-brass-400 text-navy-950 transition-colors shadow-sm font-mono">
                {{ __('ui.auth.sign_in') }}
            </button>
        </div>

        <div class="text-center pt-3 border-t border-sail-200 dark:border-navy-750 text-slate-500 dark:text-slate-400">
            <span>{{ __('ui.auth.dont_have_account') }}</span>
            <a href="{{ route('register') }}" class="font-bold text-cerulean-600 dark:text-cerulean-400 hover:underline ms-1">
                {{ __('ui.nav.register') }}
            </a>
        </div>
    </form>
</x-guest-layout>
