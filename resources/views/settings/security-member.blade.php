<x-layouts.festa>
    <div class="max-w-3xl mx-auto space-y-12 animate-reveal opacity-0" style="animation-delay: 100ms">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-6 border-b border-white/10 pb-8">
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-zinc-500 uppercase tracking-widest hover:text-festa-gold transition mb-2">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Retour au profil
                </a>
                <h1 class="font-heading text-4xl md:text-5xl font-bold text-white">
                    Sécurité & Paramètres
                </h1>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="px-6 py-3 bg-white/10 text-white font-bold uppercase tracking-widest text-xs rounded-full border border-white/20 shadow-[0_0_20px_rgba(255,255,255,0.05)] hover:bg-red-500 hover:border-red-500 hover:shadow-[0_0_30px_rgba(239,68,68,0.4)] transition duration-300">
                    Déconnexion
                </button>
            </form>
        </div>

        <div class="space-y-12">
            <!-- Password -->
            <livewire:settings.member-password />

            <!-- Two Factor -->
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <livewire:settings.member-two-factor />
            @endif
        </div>
    </div>
</x-layouts.festa>
