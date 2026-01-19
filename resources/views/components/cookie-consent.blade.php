<div x-data="{ 
        show: false,
        init() {
            // Check for the cookie 'cookie_consent'
            if (!document.cookie.split(';').some(row => row.trim().startsWith('cookie_consent='))) {
                setTimeout(() => this.show = true, 1000);
            }
        },
        accept() {
            // Set cookie for 1 year
            document.cookie = 'cookie_consent=accepted; path=/; max-age=31536000; SameSite=Lax';
            this.show = false;
        },
        refuse() {
            // Set cookie for 1 year
            document.cookie = 'cookie_consent=refused; path=/; max-age=31536000; SameSite=Lax';
            this.show = false;
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 translate-y-full"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-full"
    class="fixed bottom-0 inset-x-0 z-50 p-4 md:p-6 flex justify-center pointer-events-none"
    style="display: none;"
>
    <div class="w-full max-w-4xl pointer-events-auto">
        <div class="glass-panel bg-zinc-900/90 border border-white/10 p-6 rounded-2xl shadow-2xl flex flex-col md:flex-row items-center gap-6 md:gap-8 backdrop-blur-xl">
            
            <div class="flex items-start gap-4">
                <div class="p-3 bg-festa-gold/10 rounded-full shrink-0 hidden sm:block">
                    <svg class="size-6 text-festa-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-heading text-white font-bold text-lg mb-1">{{ __('Politique de Confidentialité') }}</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed">
                        {{ __('Nous utilisons des cookies pour améliorer votre expérience.') }} 
                        <a href="{{ route('confidentialite') }}" class="text-festa-gold hover:underline font-bold transition">{{ __('En savoir plus') }}</a>.
                    </p>
                </div>
            </div>

            <div class="flex gap-3 w-full md:w-auto shrink-0">
                <button @click="refuse()" class="flex-1 md:flex-none px-6 py-3 rounded-xl border border-white/10 text-zinc-400 font-bold uppercase tracking-wider text-xs hover:bg-white/5 hover:text-white transition">
                    {{ __('Refuser') }}
                </button>
                <button @click="accept()" class="flex-1 md:flex-none px-8 py-3 rounded-xl bg-festa-gold text-festa-red-dark font-black uppercase tracking-wider text-xs hover:bg-white hover:text-black transition shadow-lg shadow-festa-gold/20">
                    {{ __('Accepter') }}
                </button>
            </div>

        </div>
    </div>
</div>