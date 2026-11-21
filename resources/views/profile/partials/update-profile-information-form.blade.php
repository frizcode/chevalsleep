<section class="space-y-4">
    <header class="pb-3 border-b border-sail-200 dark:border-navy-700">
        <h2 class="text-base font-bold text-navy-950 dark:text-sail-100 font-sans">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
            {{ __("Update your account's profile name and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        @php
            $inp = 'w-full bg-sail-50 dark:bg-navy-900 border border-sail-200 dark:border-navy-700 rounded-md px-3.5 py-2 text-xs sm:text-sm text-navy-900 dark:text-sail-100 placeholder-slate-400 focus:outline-none focus:border-brass-500 transition-colors font-mono';
        @endphp

        <div>
            <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                {{ __('Trainer Name') }} <span class="text-rose-500">*</span>
            </label>
            <input id="name" name="name" type="text" class="{{ $inp }}" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-1.5 text-xs text-rose-500" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 font-mono">
                {{ __('Email Address') }} <span class="text-rose-500">*</span>
            </label>
            <input id="email" name="email" type="email" class="{{ $inp }}" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-1.5 text-xs text-rose-500" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 p-3 rounded bg-amber-500/10 border border-amber-500/30 text-xs text-amber-600 dark:text-amber-400 font-mono">
                    <p>
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="underline hover:text-amber-700 dark:hover:text-amber-300 ml-1">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-emerald-600 dark:text-emerald-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="pt-2 flex items-center gap-4">
            <button type="submit"
                    class="px-4 py-2 rounded-md text-xs font-bold uppercase tracking-wider bg-brass-500 hover:bg-brass-400 text-navy-950 font-mono shadow-sm transition-colors">
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
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
