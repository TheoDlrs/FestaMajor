<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO Meta Tags -->
    <title>{{ $title ?? __('Festa Major 2026') }} | Saint-Cyprien</title>
    <meta name="description" content="{{ $description ?? __('Découvrez la Festa Major de Saint-Cyprien : 3 jours de festivités catalanes, concerts, correfocs et traditions au bord de la Méditerranée.') }}">
    <meta name="keywords" content="Festa Major, Saint-Cyprien, Festival, Catalogne, Concerts, Correfoc, Sardane, Tourisme 66, Pyrénées-Orientales">
    <meta name="author" content="Office de Tourisme de Saint-Cyprien">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title ?? __('Festa Major 2026') }} | Saint-Cyprien">
    <meta property="og:description" content="{{ $description ?? __('L\'événement de l\'année à Saint-Cyprien. Rejoignez-nous pour célébrer nos traditions et notre musique !') }}">
    <meta property="og:image" content="{{ asset('images/logo-festa.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="{{ $title ?? __('Festa Major 2026') }} | Saint-Cyprien">
    <meta property="twitter:description" content="{{ $description ?? __('L\'événement de l\'année à Saint-Cyprien. Rejoignez-nous pour célébrer nos traditions et notre musique !') }}">
    <meta property="twitter:image" content="{{ asset('images/logo-festa.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|playfair-display:400,600,700,900" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#CA8A04">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
    
    <style>
        .font-heading { font-family: 'Playfair Display', serif; }
        .glass-panel { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .text-gradient-gold { background: linear-gradient(to right, #FACC15, #FFF7ED, #CA8A04); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-size: 200% auto; animation: shine 4s linear infinite; }
        .text-gradient-red { background: linear-gradient(to right, #CE1126, #EF4444, #8E0A1B); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-size: 200% auto; animation: shine 4s linear infinite; }
        @keyframes shine { to { background-position: 200% center; } }
    </style>
    @stack('head')
</head>
<body class="bg-zinc-950 text-zinc-100 antialiased font-sans selection:bg-festa-red selection:text-white flex flex-col min-h-screen">

    <!-- Global Loading Indicator -->
    <div class="fixed top-0 left-0 h-1 bg-gradient-to-r from-festa-red to-festa-gold z-[100] transition-all duration-300 ease-out"
         style="width: 0%; opacity: 0;"
         x-data="{ width: 0, show: false }"
         x-on:livewire:navigating.window="show = true; width = 30; setTimeout(() => width = 70, 500);"
         x-on:livewire:navigated.window="width = 100; setTimeout(() => { show = false; width = 0; }, 300);"
         :style="'width: ' + width + '%; opacity: ' + (show ? 1 : 0) + ';'"
    ></div>

    <!-- Navigation -->
    <div x-data="{ mobileOpen: false }" class="fixed top-0 inset-x-0 z-50 flex justify-center pt-6 pointer-events-none">
        <nav class="w-[90%] max-w-6xl rounded-full glass-panel shadow-2xl py-4 px-6 pointer-events-auto bg-zinc-950/80 flex items-center justify-between transition-all duration-700 relative z-50">
            <!-- Logo -->
            <a href="{{ route('home') }}" wire:navigate class="absolute left-1/2 -translate-x-1/2 lg:left-4 lg:translate-x-0 top-1/2 -translate-y-1/2 flex items-center justify-center group z-50">
                <div class="relative size-40 flex items-center justify-center overflow-hidden rounded-full transition duration-500 transform hover:scale-105">
                    <img src="{{ asset('images/logo-festa.png') }}" class="w-full h-full object-contain drop-shadow-2xl" alt="Logo Festa Major">
                </div>
            </a>

            <!-- Mobile Lang Switcher -->
            <div class="flex lg:hidden items-center gap-2 z-50 absolute right-16 top-1/2 -translate-y-1/2 pointer-events-auto bg-black/20 px-2 py-1 rounded-full border border-white/5">
                <a href="{{ route('lang.switch', 'fr') }}" class="text-[10px] font-black uppercase transition {{ app()->getLocale() == 'fr' ? 'text-festa-gold' : 'text-white/40 hover:text-white' }}">FR</a>
                <span class="text-white/10 text-[8px]">|</span>
                <a href="{{ route('lang.switch', 'ca') }}" class="text-[10px] font-black uppercase transition {{ app()->getLocale() == 'ca' ? 'text-festa-gold' : 'text-white/40 hover:text-white' }}">CAT</a>
            </div>

            <!-- Desktop Links -->
            <div class="hidden lg:flex items-center gap-8 ml-auto">
                <div class="flex items-center gap-6 text-xs font-bold uppercase tracking-widest text-white/70">
                    <a href="{{ route('home') }}#programme" wire:navigate class="hover:text-white hover:scale-105 transition duration-300">{{ __('Programme') }}</a>
                    <a href="{{ route('home') }}#esprit" wire:navigate class="hover:text-white hover:scale-105 transition duration-300">{{ __('L\'Esprit') }}</a>
                    <a href="{{ route('home') }}#galerie" wire:navigate class="hover:text-white hover:scale-105 transition duration-300">{{ __('Galerie') }}</a>
                    <a href="{{ route('plan-festa') }}" wire:navigate class="hover:text-white hover:scale-105 transition duration-300">{{ __('Plan Festa') }}</a>
                    <a href="{{ route('boutique') }}" wire:navigate class="hover:text-white hover:scale-105 transition duration-300">{{ __('Boutique') }}</a>
                </div>
                
                <div class="h-4 w-px bg-white/10"></div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('lang.switch', 'fr') }}" class="text-[10px] font-black uppercase transition {{ app()->getLocale() == 'fr' ? 'text-festa-gold' : 'text-white/40 hover:text-white' }}">FR</a>
                    <span class="text-white/10">|</span>
                    <a href="{{ route('lang.switch', 'ca') }}" class="text-[10px] font-black uppercase transition {{ app()->getLocale() == 'ca' ? 'text-festa-gold' : 'text-white/40 hover:text-white' }}">CAT</a>
                </div>

                <div class="h-4 w-px bg-white/10"></div>

                <div class="flex items-center gap-4">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="hidden md:inline-flex px-4 py-2 bg-zinc-800 text-white text-xs font-bold uppercase tracking-widest rounded-full hover:bg-zinc-700 transition">{{ __('Panel Admin') }}</a>
                        @endif
                    @endauth

                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-xs font-bold uppercase tracking-widest text-white hover:text-festa-gold transition">{{ __('Mon Compte') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs font-bold uppercase tracking-widest text-white hover:text-festa-gold transition px-2 py-1">{{ __('Connexion') }}</a>
                            
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="group relative px-5 py-2 overflow-hidden rounded-full bg-festa-gold text-festa-red-dark text-xs font-black uppercase tracking-widest shadow-lg shadow-festa-gold/20 hover:shadow-festa-gold/40 transition duration-300">
                                    <span class="relative z-10">{{ __('S\'inscrire') }}</span>
                                    <div class="absolute inset-0 bg-white/30 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileOpen = !mobileOpen" aria-label="{{ __('Ouvrir le menu') }}" class="lg:hidden text-white p-2 focus:outline-none z-50 relative pointer-events-auto">
                <div class="w-6 h-5 flex flex-col justify-between">
                    <span :class="mobileOpen ? 'rotate-45 translate-y-2' : ''" class="w-full h-0.5 bg-white transition-all duration-300 origin-center"></span>
                    <span :class="mobileOpen ? 'opacity-0' : ''" class="w-full h-0.5 bg-white transition-all duration-300"></span>
                    <span :class="mobileOpen ? '-rotate-45 -translate-y-2' : ''" class="w-full h-0.5 bg-white transition-all duration-300 origin-center"></span>
                </div>
            </button>
        </nav>

        <!-- Mobile Fullscreen Menu Overlay -->
        <div x-show="mobileOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 backdrop-blur-none"
             x-transition:enter-end="opacity-100 backdrop-blur-xl"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 backdrop-blur-xl"
             x-transition:leave-end="opacity-0 backdrop-blur-none"
             class="fixed inset-0 z-40 bg-zinc-950/95 flex flex-col items-center justify-center pointer-events-auto lg:hidden">
            
            <div class="flex flex-col items-center gap-8 text-center">
                <a href="{{ route('home') }}#programme" wire:navigate @click="mobileOpen = false" class="font-heading text-4xl text-white font-bold hover:text-festa-gold transition">{{ __('Programme') }}</a>
                <a href="{{ route('home') }}#esprit" wire:navigate @click="mobileOpen = false" class="font-heading text-4xl text-white font-bold hover:text-festa-gold transition">{{ __('L\'Esprit') }}</a>
                <a href="{{ route('home') }}#galerie" wire:navigate @click="mobileOpen = false" class="font-heading text-4xl text-white font-bold hover:text-festa-gold transition">{{ __('Galerie') }}</a>
                <a href="{{ route('plan-festa') }}" wire:navigate @click="mobileOpen = false" class="font-heading text-4xl text-white font-bold hover:text-festa-gold transition">{{ __('Plan Festa') }}</a>
                <a href="{{ route('boutique') }}" wire:navigate @click="mobileOpen = false" class="font-heading text-4xl text-white font-bold hover:text-festa-gold transition">{{ __('Boutique') }}</a>
                
                <div class="w-12 h-px bg-white/10 my-4"></div>
                
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-6 py-2 bg-zinc-800 text-white text-sm font-bold uppercase tracking-widest rounded-full mb-4">{{ __('Panel Admin') }}</a>
                    @endif
                @endauth

                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-bold uppercase tracking-widest text-white/70">{{ __('Mon Compte') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold uppercase tracking-widest text-white/70">{{ __('Connexion') }}</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-8 py-3 bg-festa-gold text-festa-red-dark text-sm font-black uppercase tracking-widest rounded-full">{{ __('S\'inscrire') }}</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow pt-32 pb-20 px-6">
        <div class="max-w-4xl mx-auto">
            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-zinc-950 pt-20 pb-10 border-t border-white/5 relative overflow-hidden">
        <!-- Background Ambiance -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-radial-gradient from-festa-red/5 to-transparent opacity-30 blur-[100px]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="grid md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-2">
                    <a href="{{ route('home') }}" class="inline-block mb-6">
                        <div class="w-64 flex items-center justify-start rounded-xl overflow-hidden">
                             <img src="{{ asset('images/logo-festa.png') }}" class="w-full h-auto object-contain" alt="Logo Festa Major">
                        </div>
                    </a>
                    <p class="text-zinc-500 text-sm max-w-sm mb-4">
                        {{ __('Célébrons ensemble nos racines, notre musique et notre avenir sous le ciel catalan.') }}
                    </p>
                    <div class="text-sm text-zinc-400 space-y-1 font-medium">
                        <p>{{ __('Office de Tourisme') }}</p>
                        <p>{{ __('Quai Arthur Rimbaud') }}, 66750 Saint-Cyprien</p>
                        <p>04 68 21 01 33 &bull; contact@otstcyp.com</p>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-4">{{ __('Liens') }}</h4>
                    <ul class="space-y-2 text-sm text-zinc-400">
                        <li><a href="{{ route('mentions-legales') }}" class="hover:text-festa-gold transition">{{ __('Mentions Légales') }}</a></li>
                        <li><a href="{{ route('confidentialite') }}" class="hover:text-festa-gold transition">{{ __('Confidentialité') }}</a></li>
                        <li><a href="{{ route('plan-du-site') }}" class="hover:text-festa-gold transition">{{ __('Plan du site') }}</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white text-xs font-bold uppercase tracking-wider mb-4">{{ __('Social') }}</h4>
                    <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mb-3">{{ __('Suivez-nous !') }}</p>
                    <div class="flex gap-3">
                        <a href="https://www.facebook.com/OfficeDeTourisme.SaintCyprien" target="_blank" aria-label="Facebook" class="group relative size-9 rounded-full bg-zinc-900 border border-white/10 flex items-center justify-center hover:border-festa-red transition duration-300">
                             <svg class="size-4 text-zinc-400 group-hover:text-white transition" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></svg>
                        </a>
                        <a href="https://www.instagram.com/saintcyprien/" target="_blank" aria-label="Instagram" class="group relative size-9 rounded-full bg-zinc-900 border border-white/10 flex items-center justify-center hover:border-festa-gold transition duration-300">
                            <svg class="size-4 text-zinc-400 group-hover:text-white transition" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"></svg>
                        </a>
                        <a href="https://www.youtube.com/@SaintCyprienTourisme" target="_blank" aria-label="YouTube" class="group relative size-9 rounded-full bg-zinc-900 border border-white/10 flex items-center justify-center hover:border-red-600 transition duration-300">
                            <svg class="size-4 text-zinc-400 group-hover:text-white transition" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.016 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"></svg>
                        </a>
                        <a href="https://www.linkedin.com/company/ot-saint-cyprien/" target="_blank" aria-label="LinkedIn" class="group relative size-9 rounded-full bg-zinc-900 border border-white/10 flex items-center justify-center hover:border-blue-600 transition duration-300">
                            <svg class="size-4 text-zinc-400 group-hover:text-white transition" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"></svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="text-center text-xs text-zinc-600 uppercase tracking-widest pt-8 border-t border-white/5">
                &copy; 2026 {{ __('Mairie de Saint-Cyprien. Tous droits réservés.') }}
            </div>
        </div>
    </footer>

    @fluxScripts
    <livewire:flash-alert-popup />
    <x-cookie-consent />
</body>
</html>
