<section class="space-y-4">
    <header class="pb-3 border-b border-sail-200 dark:border-navy-700">
        <h2 class="text-base font-bold text-navy-950 dark:text-sail-100 font-sans">
            {{ __('Update Security Password') }}
        </h2>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        @php
            $inp = 'w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3.5 py-2 text-xs sm:text-sm text-navy-900 dark:text-sail-100 placeholder-slate-400 focus:outline-none focus:border-brass-500 transition-colors font-mono';
        @endphp

        <div>
            <label for="update_password_current_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                {{ __('Current Password') }} <span class="text-rose-500">*</span>
            </label>
            <input id="update_password_current_password" name="current_password" type="password" class="{{ $inp }}" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <div>
            <label for="update_password_password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                {{ __('New Password') }} <span class="text-rose-500">*</span>
            </label>
            <input id="update_password_password" name="password" type="password" class="{{ $inp }}" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                {{ __('Confirm Password') }} <span class="text-rose-500">*</span>
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="{{ $inp }}" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5 text-xs text-rose-500" />
        </div>

        <div class="pt-2 flex items-center gap-4">
            <button type="submit"
                    class="px-4 py-2 rounded-md text-xs font-bold uppercase tracking-wider bg-brass-500 hover:bg-brass-400 text-navy-950 font-mono shadow-sm transition-colors">
                {{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400"
                >&check; {{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
