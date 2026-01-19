<x-layouts.auth>
    <div class="flex flex-col gap-4">
        <div class="text-center">
            <h1 class="font-heading text-3xl font-bold text-white mb-2">{{ __('Connexion') }}</h1>
            <p class="text-zinc-400 text-sm">{{ __('Accédez à votre espace festivalier') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="votre@email.com"
                class="!bg-white/5 !border-white/10 !text-white focus:!border-festa-gold focus:!ring-festa-gold/50"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Mot de passe')"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    viewable
                    class="!bg-white/5 !border-white/10 !text-white focus:!border-festa-gold focus:!ring-festa-gold/50"
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 right-0 text-xs text-festa-gold hover:text-white transition" :href="route('password.request')" wire:navigate>
                        {{ __('Oublié ?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Rester connecté')" :checked="old('remember')" class="!text-white" />

            <div class="pt-2">
                <button type="submit" class="w-full py-3 bg-festa-gold text-festa-red-dark font-bold uppercase tracking-widest text-sm rounded-xl shadow-lg hover:bg-white hover:text-festa-red transition duration-300 transform hover:-translate-y-0.5">
                    {{ __('Se connecter') }}
                </button>
            </div>
        </form>

            @if (Route::has('register'))
                <div class="text-center text-sm text-zinc-400">
                    <span>{{ __('Pas encore de compte ?') }}</span>
                    <a href="{{ route('register') }}" class="font-bold text-white hover:text-festa-gold transition ml-1" wire:navigate>{{ __('Créer un compte') }}</a>
                </div>
            @endif    </div>
</x-layouts.auth>
