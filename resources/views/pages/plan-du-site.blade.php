<x-layouts.festa title="{{ __('Plan du site') }}">
    <div class="mb-12 text-center">
        <h1 class="font-heading text-4xl md:text-5xl font-bold text-white mb-4">{{ __('Plan du site') }}</h1>
        <div class="w-24 h-1 bg-festa-gold mx-auto"></div>
    </div>

    <div class="grid md:grid-cols-2 gap-8">
        <!-- Principal -->
        <div class="glass-panel p-8 rounded-2xl border border-white/10">
            <h2 class="font-heading text-2xl text-festa-red mb-6 border-b border-white/10 pb-2">{{ __('Navigation Principale') }}</h2>
            <ul class="space-y-4">
                <li>
                    <a href="{{ route('home') }}" class="flex items-center gap-3 text-zinc-300 hover:text-white transition group">
                        <span class="size-2 rounded-full bg-festa-gold group-hover:scale-150 transition"></span>
                        <span class="font-bold uppercase tracking-wider text-sm">{{ __('Accueil') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('home') }}#programme" class="flex items-center gap-3 text-zinc-300 hover:text-white transition group">
                        <span class="size-2 rounded-full bg-zinc-700 group-hover:bg-festa-gold transition"></span>
                        <span class="uppercase tracking-wider text-sm">{{ __('Le Programme') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('home') }}#esprit" class="flex items-center gap-3 text-zinc-300 hover:text-white transition group">
                        <span class="size-2 rounded-full bg-zinc-700 group-hover:bg-festa-gold transition"></span>
                        <span class="uppercase tracking-wider text-sm">{{ __('L\'Esprit / Traditions') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('home') }}#galerie" class="flex items-center gap-3 text-zinc-300 hover:text-white transition group">
                        <span class="size-2 rounded-full bg-zinc-700 group-hover:bg-festa-gold transition"></span>
                        <span class="uppercase tracking-wider text-sm">{{ __('Galerie Photos') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('home') }}#contact" class="flex items-center gap-3 text-zinc-300 hover:text-white transition group">
                        <span class="size-2 rounded-full bg-zinc-700 group-hover:bg-festa-gold transition"></span>
                        <span class="uppercase tracking-wider text-sm">{{ __('Contact') }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Espace Membre -->
        <div class="glass-panel p-8 rounded-2xl border border-white/10">
            <h2 class="font-heading text-2xl text-festa-red mb-6 border-b border-white/10 pb-2">{{ __('Espace Membre') }}</h2>
            <ul class="space-y-4">
                <li>
                    <a href="{{ route('login') }}" class="flex items-center gap-3 text-zinc-300 hover:text-white transition group">
                        <span class="size-2 rounded-full bg-zinc-700 group-hover:bg-festa-gold transition"></span>
                        <span class="uppercase tracking-wider text-sm">{{ __('Connexion') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('register') }}" class="flex items-center gap-3 text-zinc-300 hover:text-white transition group">
                        <span class="size-2 rounded-full bg-zinc-700 group-hover:bg-festa-gold transition"></span>
                        <span class="uppercase tracking-wider text-sm">{{ __('Inscription') }}</span>
                    </a>
                </li>
                @auth
                <li>
                    <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 text-zinc-300 hover:text-white transition group">
                        <span class="size-2 rounded-full bg-festa-gold group-hover:scale-150 transition"></span>
                        <span class="font-bold uppercase tracking-wider text-sm">{{ __('Tableau de bord') }}</span>
                    </a>
                </li>
                @endauth
            </ul>
        </div>

        <!-- Informations Légales -->
        <div class="glass-panel p-8 rounded-2xl border border-white/10 md:col-span-2">
            <h2 class="font-heading text-2xl text-festa-red mb-6 border-b border-white/10 pb-2">{{ __('Informations') }}</h2>
            <div class="grid sm:grid-cols-3 gap-4">
                <a href="{{ route('mentions-legales') }}" class="text-zinc-400 hover:text-festa-gold transition text-sm">{{ __('Mentions Légales') }}</a>
                <a href="{{ route('confidentialite') }}" class="text-zinc-400 hover:text-festa-gold transition text-sm">{{ __('Politique de Confidentialité') }}</a>
                <a href="{{ route('plan-du-site') }}" class="text-zinc-400 hover:text-festa-gold transition text-sm">{{ __('Plan du site') }}</a>
            </div>
        </div>
    </div>
</x-layouts.festa>
