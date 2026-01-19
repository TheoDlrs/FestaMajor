<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO -->
    <title>{{ __('Festa Major 2026') }} | Saint-Cyprien - {{ __('Site Officiel') }}</title>
    <meta name="description" content="{{ __('Bienvenue sur le site officiel de la Festa Major de Saint-Cyprien. Découvrez le programme, les artistes et les traditions catalanes.') }}">
    <meta name="keywords" content="Festa Major, Saint-Cyprien, Concerts, Feu d'artifice, Castellers, Catalogne">
    
    <!-- Open Graph -->
    <meta property="og:title" content="Festa Major 2026 | Saint-Cyprien">
    <meta property="og:description" content="3 jours de fête, de musique et de traditions au bord de la mer.">
    <meta property="og:image" content="{{ asset('images/logo-festa.png') }}">
    <meta property="og:url" content="{{ route('home') }}">
    
    <link rel="canonical" href="{{ url()->current() }}" />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|playfair-display:400,600,700,900" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>

    <!-- JSON-LD SEO -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "Festival",
      "name": "Festa Major 2026",
      "description": "3 jours de fête, de musique et de traditions catalanes au bord de la mer à Saint-Cyprien.",
      "startDate": "2026-09-18T18:00:00+02:00",
      "endDate": "2026-09-20T23:59:00+02:00",
      "eventStatus": "https://schema.org/EventScheduled",
      "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
      "location": {
        "@@type": "Place",
        "name": "Saint-Cyprien",
        "address": {
          "@@type": "PostalAddress",
          "streetAddress": "Quai Arthur Rimbaud",
          "addressLocality": "Saint-Cyprien",
          "postalCode": "66750",
          "addressCountry": "FR"
        }
      },
      "image": [
        "{{ asset('images/logo-festa.png') }}",
        "https://images.unsplash.com/photo-1492684223066-81342ee5ff30"
      ],
      "organizer": {
        "@@type": "Organization",
        "name": "Office de Tourisme de Saint-Cyprien",
        "url": "https://www.tourisme-saint-cyprien.com"
      }
    }
    </script>
    
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
</head>
<body class="bg-zinc-950 text-zinc-100 antialiased font-sans selection:bg-festa-red selection:text-white">

    <!-- Global Loading Indicator -->
    <div class="fixed top-0 left-0 h-1 bg-gradient-to-r from-festa-red to-festa-gold z-[100] transition-all duration-300 ease-out"
         style="width: 0%; opacity: 0;"
         x-data="{ width: 0, show: false }"
         x-on:livewire:navigating.window="show = true; width = 30; setTimeout(() => width = 70, 500);"
         x-on:livewire:navigated.window="width = 100; setTimeout(() => { show = false; width = 0; }, 300);"
         :style="'width: ' + width + '%; opacity: ' + (show ? 1 : 0) + ';'"
    ></div>

    <div x-data="{ scrolled: false, mobileOpen: false }" 
         @scroll.window="scrolled = (window.pageYOffset > 50)"
         class="fixed top-0 inset-x-0 z-50 flex justify-center transition-all duration-700 ease-[cubic-bezier(0.19,1,0.22,1)]"
         :class="scrolled ? 'pt-4 pointer-events-none' : 'pt-6 md:pt-8 pointer-events-none'">
        
        <nav :class="scrolled ? 'w-[90%] max-w-6xl rounded-full glass-panel shadow-2xl py-4 px-6 pointer-events-auto bg-zinc-950/80' : 'w-full max-w-7xl px-6 py-2 bg-transparent pointer-events-auto'"
             class="flex items-center justify-between transition-all duration-700 relative z-50">
            
            <!-- Mobile Menu Button (LEFT on mobile) -->
            <button @click="mobileOpen = !mobileOpen" aria-label="{{ __('Ouvrir le menu') }}" class="lg:hidden text-white p-2 focus:outline-none z-50 relative pointer-events-auto">
                <div class="w-6 h-5 flex flex-col justify-between">
                    <span :class="mobileOpen ? 'rotate-45 translate-y-2' : ''" class="w-full h-0.5 bg-white transition-all duration-300 origin-center"></span>
                    <span :class="mobileOpen ? 'opacity-0' : ''" class="w-full h-0.5 bg-white transition-all duration-300"></span>
                    <span :class="mobileOpen ? '-rotate-45 -translate-y-2' : ''" class="w-full h-0.5 bg-white transition-all duration-300 origin-center"></span>
                </div>
            </button>

            <!-- Logo (CENTER on mobile) -->
            <a href="#" class="absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 lg:left-4 lg:translate-x-0 flex items-center justify-center group z-50">
                <div class="relative size-32 md:size-40 flex items-center justify-center overflow-hidden rounded-full transition duration-500 transform hover:scale-105">
                    <img src="{{ asset('images/logo-festa.png') }}" class="w-full h-full object-contain drop-shadow-2xl" alt="Logo Festa Major">
                </div>
            </a>

            <!-- Mobile Lang Switcher (RIGHT on mobile) -->
            <div class="flex lg:hidden items-center gap-2 z-50 absolute right-6 top-1/2 -translate-y-1/2 pointer-events-auto bg-black/40 px-3 py-1.5 rounded-full border border-white/10 backdrop-blur-md">
                <a href="{{ route('lang.switch', 'fr') }}" class="text-[10px] font-black uppercase transition {{ app()->getLocale() == 'fr' ? 'text-festa-gold' : 'text-white/40 hover:text-white' }}">FR</a>
                <span class="text-white/10 text-[8px]">|</span>
                <a href="{{ route('lang.switch', 'ca') }}" class="text-[10px] font-black uppercase transition {{ app()->getLocale() == 'ca' ? 'text-festa-gold' : 'text-white/40 hover:text-white' }}">CAT</a>
            </div>

            <!-- Desktop Links -->
            <div class="hidden lg:flex items-center gap-4 lg:gap-8 ml-auto">
                <div class="flex items-center gap-4 lg:gap-6 text-xs font-bold uppercase tracking-widest text-white/70">
                    <a href="#programme" class="hover:text-white hover:scale-105 transition duration-300">{{ __('Programme') }}</a>
                    <a href="#esprit" class="hover:text-white hover:scale-105 transition duration-300">{{ __('L\'Esprit') }}</a>
                    <a href="#galerie" class="hover:text-white hover:scale-105 transition duration-300">{{ __('Galerie') }}</a>
                    <a href="{{ route('plan-festa') }}" class="hover:text-white hover:scale-105 transition duration-300">{{ __('Plan Festa') }}</a>
                    <a href="{{ route('boutique') }}" class="hover:text-white hover:scale-105 transition duration-300">{{ __('Boutique') }}</a>
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
        </nav>

        <div x-show="mobileOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 backdrop-blur-none"
             x-transition:enter-end="opacity-100 backdrop-blur-xl"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 backdrop-blur-xl"
             x-transition:leave-end="opacity-0 backdrop-blur-none"
             class="fixed inset-0 z-40 bg-zinc-950/95 flex flex-col items-center justify-center pointer-events-auto lg:hidden">
            
            <div class="flex flex-col items-center gap-8 text-center">
                <a href="#programme" @click="mobileOpen = false" class="font-heading text-4xl text-white font-bold hover:text-festa-gold transition">{{ __('Programme') }}</a>
                <a href="#esprit" @click="mobileOpen = false" class="font-heading text-4xl text-white font-bold hover:text-festa-gold transition">{{ __('L\'Esprit') }}</a>
                <a href="#galerie" @click="mobileOpen = false" class="font-heading text-4xl text-white font-bold hover:text-festa-gold transition">{{ __('Galerie') }}</a>
                <a href="{{ route('plan-festa') }}" @click="mobileOpen = false" class="font-heading text-4xl text-white font-bold hover:text-festa-gold transition">{{ __('Plan Festa') }}</a>
                <a href="{{ route('boutique') }}" @click="mobileOpen = false" class="font-heading text-4xl text-white font-bold hover:text-festa-gold transition">{{ __('Boutique') }}</a>
                
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

    <header class="relative h-screen flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=2070&auto=format&fit=crop" 
                 alt="Festa Major Ambiance" 
                 fetchpriority="high"
                 class="w-full h-full object-cover brightness-[0.35] scale-105 animate-float origin-center" 
                 style="animation-duration: 20s" />
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/20 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-festa-red/10 to-festa-gold/5 mix-blend-overlay"></div>
        </div>

        <div class="relative z-10 text-center px-4 max-w-5xl mx-auto mt-10"
             x-data="{ 
                days: '00', hours: '00', minutes: '00', seconds: '00',
                started: false,
                timer: null,
                init() {
                    // --- MODE RÉEL ---
                    const targetDate = new Date('September 18, 2026 18:00:00').getTime();

                    this.timer = setInterval(() => {
                        const now = new Date().getTime();
                        const distance = targetDate - now;

                        if (distance < 0) {
                            clearInterval(this.timer);
                            this.started = true;
                            this.launchFireworks();
                            return;
                        }
                        
                        this.days = Math.floor(distance / (1000 * 60 * 60 * 24)).toString().padStart(2, '0');
                        this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)).toString().padStart(2, '0');
                        this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)).toString().padStart(2, '0');
                        this.seconds = Math.floor((distance % (1000 * 60)) / 1000).toString().padStart(2, '0');
                    }, 1000);
                },
                launchFireworks() {
                    const duration = 5 * 1000;
                    const animationEnd = Date.now() + duration;
                    const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 100 };

                    const randomInRange = (min, max) => Math.random() * (max - min) + min;

                    const interval = setInterval(function() {
                        const timeLeft = animationEnd - Date.now();

                        if (timeLeft <= 0) {
                            return clearInterval(interval);
                        }

                        const particleCount = 50 * (timeLeft / duration);
                        
                        confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 }, colors: ['#CE1126', '#FACC15'] });
                        confetti({ ...defaults, particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 }, colors: ['#CE1126', '#FACC15'] });
                    }, 250);
                }
            }">
            
            <!-- STACKING CONTAINER -->
            <div class="grid grid-cols-1 items-center justify-items-center min-h-[400px]">
                
                <!-- ETAT 1 : ATTENTE (Titre + Compteur) -->
                <div x-show="!started" 
                     x-transition:leave="transition ease-in-out duration-1000"
                     x-transition:leave-start="opacity-100 transform scale-100 blur-0"
                     x-transition:leave-end="opacity-0 transform scale-95 blur-xl"
                     class="col-start-1 row-start-1 w-full flex flex-col items-center">
                    
                    <!-- Date Badge -->
                    <div class="animate-reveal opacity-0" style="animation-delay: 100ms">
                        <div class="inline-flex items-center gap-3 px-5 py-2 rounded-full glass-panel mb-8 border-festa-gold/20">
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-festa-gold opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-festa-gold"></span>
                            </span>
                            <span class="text-festa-gold text-xs font-black uppercase tracking-[0.25em]">18 — 20 {{ __('Septembre') }} 2026</span>
                        </div>
                    </div>

                    <!-- Main Title -->
                    <h1 class="animate-reveal opacity-0 font-heading text-6xl md:text-8xl lg:text-[9rem] font-black text-white mb-6 leading-[0.9] tracking-tighter drop-shadow-2xl" 
                        style="animation-delay: 300ms">
                        <span class="text-gradient-red">FESTA</span> <span class="text-gradient-gold">MAJOR</span>
                    </h1>

                    <!-- Countdown -->
                    <div class="animate-reveal opacity-0 mb-12 min-h-[120px] flex items-center justify-center" style="animation-delay: 500ms">
                        <div class="flex justify-center items-center gap-4 md:gap-10">
                            <div class="flex flex-col items-center">
                                <span class="text-5xl md:text-7xl font-black font-heading text-gradient-red" x-text="days"></span>
                                <span class="text-[10px] uppercase font-bold text-festa-gold tracking-widest mt-2">{{ __('Jours') }}</span>
                            </div>
                            <div class="text-3xl md:text-5xl font-light text-white/20 self-start pt-2">:</div>
                            <div class="flex flex-col items-center">
                                <span class="text-5xl md:text-7xl font-black font-heading text-gradient-red" x-text="hours"></span>
                                <span class="text-[10px] uppercase font-bold text-festa-gold tracking-widest mt-2">{{ __('Heures') }}</span>
                            </div>
                            <div class="text-3xl md:text-5xl font-light text-white/20 self-start pt-2">:</div>
                            <div class="flex flex-col items-center">
                                <span class="text-5xl md:text-7xl font-black font-heading text-gradient-gold" x-text="minutes"></span>
                                <span class="text-[10px] uppercase font-bold text-festa-gold tracking-widest mt-2">{{ __('Minutes') }}</span>
                            </div>
                            <div class="text-3xl md:text-5xl font-light text-white/20 self-start pt-2">:</div>
                            <div class="flex flex-col items-center">
                                <span class="text-5xl md:text-7xl font-black font-heading text-gradient-gold" x-text="seconds"></span>
                                <span class="text-[10px] uppercase font-bold text-festa-gold tracking-widest mt-2">{{ __('Secondes') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ETAT 2 : FÊTE (Message Géant) -->
                <div x-show="started" 
                     style="display: none;"
                     x-transition:enter="transition ease-out duration-1000 delay-500"
                     x-transition:enter-start="opacity-0 transform scale-110 blur-xl"
                     x-transition:enter-end="opacity-100 transform scale-100 blur-0"
                     class="col-start-1 row-start-1 w-full flex flex-col items-center justify-center z-20">
                    
                    <h2 class="font-heading text-6xl md:text-8xl lg:text-9xl font-black text-white leading-none drop-shadow-[0_0_80px_rgba(250,204,21,0.8)] text-center">
                        <span class="text-gradient-red block mb-4">LA FESTA</span>
                        <span class="text-gradient-gold">COMMENCE !</span>
                    </h2>
                    
                    <div class="mt-12 animate-pulse">
                        <p class="text-white/80 font-bold uppercase tracking-[0.5em] text-xl">
                            {{ __('Bienvenue à Saint-Cyprien') }}
                        </p>
                    </div>
                </div>

            </div>

            <!-- Static Footer Content -->
            <div class="transition-opacity duration-1000" :class="started ? 'opacity-0 pointer-events-none' : 'opacity-100'">
                <p class="animate-reveal opacity-0 text-lg md:text-2xl text-zinc-300 font-light max-w-2xl mx-auto mb-10 leading-relaxed" style="animation-delay: 700ms">
                    {{ __('L\'embrasement de Saint-Cyprien. Trois nuits de légendes, de feux et de passions catalanes.') }}
                </p>
                
                <div class="animate-reveal opacity-0 flex flex-col sm:flex-row gap-5 justify-center items-center" style="animation-delay: 900ms">
                    <a href="#programme" class="px-8 py-4 bg-festa-red text-white font-bold uppercase tracking-widest text-sm rounded-full shadow-[0_0_30px_rgba(206,17,38,0.4)] hover:shadow-[0_0_50px_rgba(206,17,38,0.6)] hover:bg-festa-red-dark transition-all duration-300 transform hover:-translate-y-1">
                        {{ __('Découvrir le Programme') }}
                    </a>
                    @if (Route::has('login'))
                        @guest
                        <a href="{{ route('login') }}" class="px-8 py-4 glass-panel text-white font-bold uppercase tracking-widest text-sm rounded-full hover:bg-white/10 transition-all duration-300">
                            {{ __('Connexion') }}
                        </a>
                        @endguest
                    @endif
                </div>
            </div>
        </div>
        
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 flex flex-col items-center gap-2 opacity-50 animate-bounce">
            <span class="text-[10px] uppercase tracking-widest">{{ __('Scroll') }}</span>
            <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
        </div>
    </header>

    <section id="esprit" class="py-32 relative overflow-hidden bg-zinc-950">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-festa-gold/5 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-festa-red/5 rounded-full blur-[120px] pointer-events-none"></div>

<div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
    <div class="grid lg:grid-cols-2 gap-20 items-center">
        
        <div class="order-2 lg:order-1 relative group transform rotate-2 group-hover:rotate-0 transition duration-700 ease-out">
            <div class="absolute -inset-4 bg-gradient-to-tr from-festa-red to-festa-gold rounded-3xl opacity-20 blur-lg group-hover:opacity-30 transition duration-700"></div>
            
            <div class="relative rounded-2xl shadow-2xl overflow-hidden bg-zinc-900">
                @php
                    $activeFlyer = \App\Models\Flyer::where('is_active', true)->latest()->first();
                @endphp
                <img src="{{ $activeFlyer ? $activeFlyer->image_url : 'https://www.escapadeslr.com/img/agenda/1356-festa-major-saint-cyprien-2.jpg' }}" 
                     alt="{{ $activeFlyer ? $activeFlyer->title : __('Flyer Festa Major') }}" 
                     loading="lazy"
                     class="w-full h-full object-contain aspect-[4/5]" />
            </div>
            
            <div class="absolute -bottom-6 -right-6 bg-white text-zinc-900 p-6 rounded-2xl shadow-xl max-w-xs hidden md:block z-20">
                <p class="font-heading font-bold text-lg mb-1">"{{ __('La force de l\'unité.') }}"</p>
                <p class="text-xs text-zinc-500 uppercase tracking-widest">{{ __('Devise Castellers') }}</p>
            </div>
        </div>

        <div class="order-1 lg:order-2 space-y-8">
            <div>
                <h2 class="text-festa-gold text-xs font-black uppercase tracking-[0.4em] mb-4">{{ __('L\'Héritage') }}</h2>
                <h3 class="font-heading text-5xl md:text-6xl font-black text-white leading-none">
                    {{ __('Terre de') }} <br> <span class="text-festa-red italic">{{ __('Feu & d\'Or') }}</span>.
                </h3>
            </div>
            
            <p class="text-lg text-zinc-400 leading-relaxed border-l-2 border-festa-gold/30 pl-6">
                {{ __('La Festa Major n\'est pas un simple festival. C\'est le moment où Saint-Cyprien renoue avec ses racines. Des Correfocs qui illuminent les ruelles aux Sardanes sur le parvis, chaque instant est une célébration de notre identité catalane.') }}
            </p>

            <div class="grid grid-cols-3 gap-6 pt-6 border-t border-white/10">
                <div class="text-center">
                    <span class="block font-heading text-3xl text-white font-bold">1200</span>
                    <span class="text-[10px] uppercase tracking-widest text-zinc-600 font-bold">{{ __('Participants') }}</span>
                </div>
                <div class="text-center border-l border-white/10">
                    <span class="block font-heading text-3xl text-white font-bold">45</span>
                    <span class="text-[10px] uppercase tracking-widest text-zinc-600 font-bold">{{ __('Spectacles') }}</span>
                </div>
                <div class="text-center border-l border-white/10">
                    <span class="block font-heading text-3xl text-festa-gold font-bold">∞</span>
                    <span class="text-[10px] uppercase tracking-widest text-zinc-600 font-bold">{{ __('Souvenirs') }}</span>
                </div>
            </div>
        </div> </div> </div>

<section id="programme" class="py-32 bg-zinc-900">
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
            <div>
                <h2 class="text-festa-red text-xs font-black uppercase tracking-[0.4em] mb-3">{{ __('Line-up 2026') }}</h2>
                <h3 class="font-heading text-4xl font-bold text-white">{{ __('Les Temps Forts') }}</h3>
            </div>
            <div class="h-px bg-white/20 flex-1 mx-8 hidden md:block relative top-[-10px]"></div>
            <p class="text-zinc-500 text-sm max-w-xs text-right hidden md:block">
                {{ __('Trois jours intenses au cœur de Saint-Cyprien. Une programmation mêlant traditions catalanes et culture des terroirs.') }}
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            @php
                // OPTIMISATION: Mise en cache du programme pour 1 heure (3600 secondes)
                $programEvents = \Illuminate\Support\Facades\Cache::remember('program_events', 3600, function () {
                    return \App\Models\ProgramEvent::orderBy('order')->get();
                });
            @endphp

            @foreach($programEvents as $event)
                <div class="group relative aspect-[3/4] overflow-hidden rounded-2xl cursor-pointer {{ $event->is_featured ? 'ring-2 ring-festa-gold/50' : '' }}">
                    <img src="{{ $event->image_url }}" 
                         loading="lazy"
                         class="absolute inset-0 w-full h-full object-cover transition duration-700 group-hover:scale-110" 
                         alt="{{ $event->title }}">
                    <div class="absolute inset-0 bg-gradient-to-t {{ $event->is_featured ? 'from-festa-red-dark/90' : 'from-black' }} via-black/20 to-transparent opacity-80 group-hover:opacity-60 transition duration-500">
                    </div>
                    
                    @if($event->is_featured)
                        <div class="absolute top-4 right-4 bg-festa-red text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            {{ __('Événement Phare') }}
                        </div>
                    @endif

                    <div class="absolute bottom-0 left-0 p-8 w-full transform translate-y-4 group-hover:translate-y-0 transition duration-500">
                        <span class="text-festa-gold text-xs font-black uppercase tracking-widest mb-2 block">{{ $event->time }}</span>
                        <h4 class="font-heading text-3xl text-white font-bold mb-2">{{ __($event->title) }}</h4>
                        <p class="text-zinc-300 text-sm opacity-0 group-hover:opacity-100 transition duration-500 delay-100 line-clamp-2">
                            {{ __($event->description) }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="galerie" class="py-32 bg-zinc-950 relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-radial-gradient from-festa-gold/5 to-transparent opacity-30 blur-[120px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-6 lg:px-8 mb-16 relative z-10">
        <div class="text-center">
            <h2 class="text-festa-gold text-xs font-black uppercase tracking-[0.4em] mb-4">{{ __('Souvenirs') }}</h2>
            <h3 class="font-heading text-4xl md:text-5xl font-bold text-white italic">{{ __('L\'Album Officiel') }}</h3>
        </div>
    </div>
    
    <!-- Infinite Horizontal Slider -->
    <div class="relative flex overflow-hidden group py-10">
        <!-- Masking Edges -->
        <div class="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-zinc-950 to-transparent z-20 pointer-events-none"></div>
        <div class="absolute inset-y-0 right-0 w-32 bg-gradient-to-l from-zinc-950 to-transparent z-20 pointer-events-none"></div>

        <div class="flex animate-marquee group-hover:pause gap-6 whitespace-nowrap">
            @php 
                // OPTIMISATION: Mise en cache de la galerie pour 1 heure
                $images = \Illuminate\Support\Facades\Cache::remember('gallery_images', 3600, function () {
                    return \App\Models\GalleryImage::orderBy('order')->get();
                });
                // Duplicate images to ensure a smooth infinite loop
                $loopImages = $images->concat($images);
            @endphp

            @foreach($loopImages as $image)
                <div class="relative w-[300px] md:w-[450px] h-[250px] md:h-[350px] flex-shrink-0 rounded-2xl overflow-hidden border border-white/10 shadow-2xl transition-all duration-500 hover:scale-105 hover:border-festa-gold/50 hover:z-30 cursor-pointer">
                    <img src="{{ $image->image_url }}" loading="lazy" class="w-full h-full object-cover grayscale-[0.3] hover:grayscale-0 transition-all duration-700" alt="Souvenir">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60 group-hover:opacity-20 transition"></div>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animate-marquee {
            display: flex;
            width: fit-content;
            animation: marquee 40s linear infinite;
        }
        .pause {
            animation-play-state: paused;
        }
    </style>
</section>

    <footer id="contact" class="relative bg-zinc-950 pt-32 pb-10 overflow-hidden">
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-radial-gradient from-festa-red/10 to-transparent opacity-40 blur-[100px] translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-[0.03]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-20">
                <div class="lg:col-span-5 flex flex-col h-full">
                    <div class="mb-10">
                        <a href="#" class="inline-block group">
                            <div class="w-80 flex items-center justify-start rounded-2xl group-hover:scale-105 transition duration-500">
                                <img src="{{ asset('images/logo-festa.png') }}" class="w-full h-auto object-contain" alt="Logo Footer">
                            </div>
                        </a>
                        <p class="text-zinc-400 text-lg leading-relaxed mt-8 border-l-2 border-white/10 pl-6">
                            {{ __('L\'événement culturel de l\'année. Célébrons ensemble nos racines, notre musique et notre avenir sous le ciel catalan.') }}
                        </p>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-6 mb-10">
                        <div class="col-span-2 sm:col-span-2 bg-white/5 border border-white/10 rounded-2xl p-6 relative overflow-hidden group hover:border-festa-gold/30 transition duration-500">
                            <h4 class="font-heading text-lg text-white font-bold mb-4 flex items-center gap-2">
                                <svg class="size-5 text-festa-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ __('Office de Tourisme') }}
                            </h4>
                            <ul class="space-y-3 text-sm">
                                <li class="flex justify-between items-center pt-1 mb-4"><span class="text-zinc-400">{{ __('Lun - Sam') }}</span> <span class="text-white font-medium">9h-12h / 14h-18h</span></li>
                                
                                <li class="flex items-start gap-3 pt-3 border-t border-white/5">
                                    <svg class="w-4 h-4 text-zinc-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="text-zinc-300">{{ __('Quai Arthur Rimbaud') }}<br>66750 Saint-Cyprien</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    <span class="text-zinc-300">04 68 21 01 33</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <span class="text-zinc-300">contact@otstcyp.com</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mt-8 mb-3">{{ __('Suivez-nous !') }}</p>
                    <div class="mt-0 pt-0 flex gap-3">
                        <a href="https://www.facebook.com/OfficeDeTourisme.SaintCyprien" target="_blank" aria-label="Facebook" class="group relative size-10 rounded-full overflow-hidden bg-zinc-900 border border-zinc-800 flex items-center justify-center transition-all duration-300 hover:border-festa-red">
                            <div class="absolute inset-0 bg-festa-red translate-y-full group-hover:translate-y-0 transition duration-300"></div>
                            <svg class="size-4 text-zinc-400 group-hover:text-white relative z-10" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://www.instagram.com/saintcyprien/" target="_blank" aria-label="Instagram" class="group relative size-10 rounded-full overflow-hidden bg-zinc-900 border border-zinc-800 flex items-center justify-center transition-all duration-300 hover:border-festa-gold">
                            <div class="absolute inset-0 bg-festa-gold translate-y-full group-hover:translate-y-0 transition duration-300"></div>
                            <svg class="size-4 text-zinc-400 group-hover:text-festa-red-dark relative z-10" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4z"/></svg>
                        </a>
                        <a href="https://www.youtube.com/@SaintCyprienTourisme" target="_blank" aria-label="YouTube" class="group relative size-10 rounded-full overflow-hidden bg-zinc-900 border border-zinc-800 flex items-center justify-center transition-all duration-300 hover:border-red-600">
                            <div class="absolute inset-0 bg-red-600 translate-y-full group-hover:translate-y-0 transition duration-300"></div>
                            <svg class="size-4 text-zinc-400 group-hover:text-white relative z-10" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.016 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/company/ot-saint-cyprien/" target="_blank" aria-label="LinkedIn" class="group relative size-10 rounded-full overflow-hidden bg-zinc-900 border border-zinc-800 flex items-center justify-center transition-all duration-300 hover:border-blue-600">
                            <div class="absolute inset-0 bg-blue-600 translate-y-full group-hover:translate-y-0 transition duration-300"></div>
                            <svg class="size-4 text-zinc-400 group-hover:text-white relative z-10" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <livewire:partials.contact-form />
                </div>
            </div>

            <div class="mt-24 pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center gap-4 text-[10px] font-bold text-zinc-600 uppercase tracking-[0.2em]">
                <p>&copy; 2026 {{ __('Office de Tourisme') }} Saint-Cyprien</p>
                <div class="flex gap-6">
                    <a href="{{ route('mentions-legales') }}" class="hover:text-festa-gold transition">{{ __('Mentions Légales') }}</a>
                    <a href="{{ route('confidentialite') }}" class="hover:text-festa-gold transition">{{ __('Confidentialité') }}</a>
                    <a href="{{ route('plan-du-site') }}" class="hover:text-festa-gold transition">{{ __('Plan du site') }}</a>
                </div>
            </div>
        </div>
    </footer>

    @fluxScripts
    <livewire:flash-alert-popup />
    <x-cookie-consent />
</body>
</html>
