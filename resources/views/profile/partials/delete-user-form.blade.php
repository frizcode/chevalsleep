<section class="space-y-4">
    <header class="pb-3 border-b border-rose-200 dark:border-rose-900/40">
        <h2 class="text-base font-bold text-rose-600 dark:text-rose-400 font-sans">
            {{ __('Delete Account') }}
        </h2>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
            {{ __('Once your account is deleted, all of its saved bookmarks and data will be permanently wiped.') }}
        </p>
    </header>

    <div>
        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-4 py-2 rounded-md text-xs font-bold uppercase tracking-wider bg-rose-600 hover:bg-rose-500 text-white font-mono shadow-sm transition-colors"
        >{{ __('Delete Account') }}</button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white dark:bg-navy-800 border border-sail-200 dark:border-navy-700 rounded-lg space-y-4">
            @csrf
            @method('delete')

            <div>
                <h2 class="text-lg font-bold text-navy-950 dark:text-sail-100 font-sans">
                    {{ __('Are you sure you want to delete your account?') }}
                </h2>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 font-mono">
                    {{ __('Please enter your password to confirm permanent account deletion.') }}
                </p>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                    {{ __('Confirm Password') }} <span class="text-rose-500">*</span>
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3.5 py-2 text-xs sm:text-sm text-navy-900 dark:text-sail-100 placeholder-slate-400 focus:outline-none focus:border-rose-500 transition-colors font-mono"
                    placeholder="{{ __('Password') }}"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5 text-xs text-rose-500" />
            </div>

            <div class="pt-3 border-t border-sail-200 dark:border-navy-700 flex justify-end space-x-3">
                <button type="button" x-on:click="$dispatch('close')"
                        class="px-4 py-2 rounded-md text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-navy-950 dark:hover:text-sail-100 font-mono transition-colors">
                    {{ __('Cancel') }}
                </button>

                <button type="submit"
                        class="px-4 py-2 rounded-md text-xs font-bold uppercase tracking-wider bg-rose-600 hover:bg-rose-500 text-white font-mono shadow-sm transition-colors">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
