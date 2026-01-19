<x-layouts.festa 
    title="{{ __('Plan Interactif & Carte') }}" 
    description="{{ __('Retrouvez tous les lieux de la Festa Major : scènes, parkings et points d\'info sur notre carte interactive officielle.') }}">
    <div class="relative py-12">
        <div class="text-center mb-16">
            <h2 class="text-festa-gold text-xs font-black uppercase tracking-[0.4em] mb-4">{{ __('L\'Héritage') }}</h2>
            <h1 class="font-heading text-5xl md:text-7xl font-black text-white mb-6">
                {{ __('Plan') }} <span class="text-festa-red italic">Festa</span>
            </h1>
            <p class="text-zinc-400 text-lg max-w-2xl mx-auto border-l-2 border-festa-gold/30 pl-6">
                {{ __('Retrouvez tous les lieux emblématiques, les scènes et les stands de la Festa Major sur notre carte interactive.') }}
            </p>
        </div>

        <div class="grid lg:grid-cols-3 gap-8 mb-12">
            <!-- Weather Widget -->
            <div class="lg:col-span-2 relative overflow-hidden rounded-3xl bg-zinc-900/50 border border-white/10 p-6 flex flex-col md:flex-row items-center justify-between gap-6 group"
                 x-data="{ 
                    weather: null, 
                    loading: true,
                    init() {
                        fetch('https://api.open-meteo.com/v1/forecast?latitude=42.6193&longitude=3.0031&current=temperature_2m,weather_code&daily=weather_code,temperature_2m_max,temperature_2m_min&timezone=Europe%2FParis&forecast_days=3')
                            .then(res => res.json())
                            .then(data => {
                                this.weather = data;
                                this.loading = false;
                            });
                    },
                    getIcon(code) {
                        // Simple mapping for WMO weather codes
                        if (code === 0) return 'sun';
                        if (code >= 1 && code <= 3) return 'cloud-sun';
                        if (code >= 45 && code <= 48) return 'cloud';
                        if (code >= 51 && code <= 67) return 'cloud-rain';
                        if (code >= 80 && code <= 82) return 'cloud-showers-heavy';
                        if (code >= 95) return 'bolt';
                        return 'sun';
                    }
                 }">
                
                <!-- Animated Background for Weather -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent opacity-50 group-hover:opacity-100 transition duration-700"></div>
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-festa-gold/20 rounded-full blur-[80px]"></div>

                <div class="relative z-10 flex items-center gap-6">
                    <div class="p-4 bg-white/10 rounded-full backdrop-blur-md shadow-inner border border-white/20">
                         <!-- Sun Icon Placeholder (dynamic based on code ideally, using a generic sun for now if loading) -->
                         <svg x-show="!loading" class="size-10 text-festa-gold animate-pulse-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <div x-show="loading" class="size-10 rounded-full border-2 border-white/20 border-t-white animate-spin"></div>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg leading-tight">{{ __('Météo St-Cyprien') }}</h3>
                        <p class="text-zinc-400 text-xs uppercase tracking-wider">{{ __('En temps réel') }}</p>
                    </div>
                </div>

                <div class="relative z-10 flex items-center gap-8 text-center" x-show="!loading" x-transition>
                    <div>
                        <span class="block text-4xl font-black text-white" x-text="Math.round(weather.current.temperature_2m) + '°'"></span>
                        <span class="text-zinc-400 text-xs">{{ __('Actuellement') }}</span>
                    </div>
                    <div class="h-10 w-px bg-white/10"></div>
                    <div class="flex flex-col gap-1">
                        <span class="text-xs text-zinc-300 font-bold" x-text="'Max: ' + Math.round(weather.daily.temperature_2m_max[0]) + '°'"></span>
                        <span class="text-xs text-zinc-500" x-text="'Min: ' + Math.round(weather.daily.temperature_2m_min[0]) + '°'"></span>
                    </div>
                </div>
            </div>

            <!-- Download Button -->
            <a href="#" class="relative group lg:col-span-1 rounded-3xl bg-zinc-900/50 border border-white/10 p-6 flex flex-col items-center justify-center text-center overflow-hidden hover:border-festa-red/50 transition duration-500">
                <div class="absolute inset-0 bg-gradient-to-br from-festa-red/10 to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>
                
                <div class="relative z-10 mb-4 p-4 bg-festa-red/10 rounded-full group-hover:scale-110 transition duration-300">
                    <svg class="size-8 text-festa-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                </div>
                
                <h3 class="relative z-10 text-white font-bold text-lg mb-1">{{ __('Télécharger le Plan') }}</h3>
                <p class="relative z-10 text-zinc-500 text-xs">{{ __('PDF Haute Définition') }}</p>
            </a>
        </div>

        <!-- Incredible Map Container -->
        <div class="relative w-full rounded-[2.5rem] p-2 bg-gradient-to-b from-zinc-800 to-zinc-950 shadow-2xl overflow-hidden">
            <!-- Animated Border Glow -->
            <div class="absolute -inset-[2px] bg-gradient-to-r from-festa-red via-festa-gold to-festa-red rounded-[2.6rem] blur opacity-75 animate-gradient-xy"></div>
            
            <!-- Inner Container -->
            <div class="relative rounded-[2rem] overflow-hidden bg-zinc-900 border border-white/10 shadow-[inset_0_0_40px_rgba(0,0,0,0.8)]">
                
                <!-- Holographic Overlay Effects -->
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-white/50 to-transparent opacity-50 z-20 animate-scanline"></div>
                <div class="absolute inset-0 pointer-events-none z-10 mix-blend-overlay opacity-20 bg-[url('https://www.transparenttextures.com/patterns/diagmonds-light.png')]"></div>

                <div class="relative z-0 w-full aspect-[4/3] md:aspect-video min-h-[600px]" 
     x-data="{ mapUrl: 'https://www.google.com/maps/d/embed?mid=1iQM-9_7g9CcgYeZjFK4_c_HmgaH3Fdk&ehbc=2E312F' }">
    <iframe 
        :src="mapUrl + '&nocache=' + new Date().getTime()" 
        width="100%" 
        height="100%" 
        style="border:0; filter: grayscale(20%) contrast(1.1) brightness(0.9);"
        allowfullscreen="" 
        loading="lazy">
    </iframe>
</div>

                <!-- Corner Decorations -->
                <div class="absolute top-6 left-6 w-16 h-16 border-t-2 border-l-2 border-festa-gold/80 rounded-tl-xl z-20 pointer-events-none"></div>
                <div class="absolute top-6 right-6 w-16 h-16 border-t-2 border-r-2 border-festa-red/80 rounded-tr-xl z-20 pointer-events-none"></div>
                <div class="absolute bottom-6 left-6 w-16 h-16 border-b-2 border-l-2 border-festa-red/80 rounded-bl-xl z-20 pointer-events-none"></div>
                <div class="absolute bottom-6 right-6 w-16 h-16 border-b-2 border-r-2 border-festa-gold/80 rounded-br-xl z-20 pointer-events-none"></div>
                
                <!-- Floating Badge -->
                <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 px-6 py-2 bg-black/80 backdrop-blur-md rounded-full border border-festa-gold/30 text-festa-gold text-xs font-black uppercase tracking-[0.2em] shadow-lg flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-festa-red opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-festa-red"></span>
                    </span>
                    {{ __('Carte Interactive Live') }}
                </div>
            </div>
        </div>

        <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-6 bg-white/5 border border-white/10 rounded-2xl group hover:bg-white/10 transition duration-300">
                <flux:icon.map-pin class="size-8 text-festa-red mb-4 group-hover:scale-110 transition duration-300" />
                <h3 class="text-white font-bold mb-2">{{ __('Points d\'intérêt') }}</h3>
                <p class="text-zinc-500 text-sm">{{ __('Localisez les scènes de concerts, les zones de restauration et les parkings.') }}</p>
            </div>
            <div class="p-6 bg-white/5 border border-white/10 rounded-2xl group hover:bg-white/10 transition duration-300">
                <flux:icon.information-circle class="size-8 text-festa-gold mb-4 group-hover:scale-110 transition duration-300" />
                <h3 class="text-white font-bold mb-2">{{ __('Infos Pratiques') }}</h3>
                <p class="text-zinc-500 text-sm">{{ __('Cliquez sur les icônes pour obtenir plus d\'informations sur chaque lieu.') }}</p>
            </div>
            <div class="p-6 bg-white/5 border border-white/10 rounded-2xl group hover:bg-white/10 transition duration-300">
                <flux:icon.device-phone-mobile class="size-8 text-white mb-4 group-hover:scale-110 transition duration-300" />
                <h3 class="text-white font-bold mb-2">{{ __('Mobile') }}</h3>
                <p class="text-zinc-500 text-sm">{{ __('La carte est consultable directement sur votre smartphone pendant l\'événement.') }}</p>
            </div>
        </div>
    </div>

    <style>
        @keyframes gradient-xy {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        .animate-gradient-xy {
            background-size: 200% 200%;
            animation: gradient-xy 6s ease infinite;
        }
        @keyframes scanline {
            0% { top: 0%; opacity: 0; }
            50% { opacity: 0.5; }
            100% { top: 100%; opacity: 0; }
        }
        .animate-scanline {
            animation: scanline 4s linear infinite;
        }
        .animate-pulse-slow {
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</x-layouts.festa>
