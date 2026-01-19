<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark h-full">
    <head>
        @include('partials.head')
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|playfair-display:400,700,900" rel="stylesheet" />
        <style>
            .font-heading { font-family: 'Playfair Display', serif; }
            .glass-panel { background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); }
            [data-flux-label] { color: white !important; font-weight: 600 !important; }
            [data-flux-sublabel] { color: rgba(255, 255, 255, 0.7) !important; }
        </style>
    </head>
    <body class="min-h-screen bg-zinc-950 text-white antialiased h-full flex items-center justify-center relative overflow-hidden">
        
        <!-- Background -->
        <div class="absolute inset-0 z-0">
             <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover opacity-30" alt="Background">
             <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/60 to-zinc-950/80"></div>
        </div>

        <div class="relative z-10 w-full {{ $maxWidth ?? 'max-w-md' }} p-6">
            <div class="flex flex-col items-center gap-2 glass-panel py-6 px-8 md:py-8 md:px-10 rounded-3xl shadow-2xl border-t border-white/10">
                <a href="{{ route('home') }}" class="group transition duration-300 mb-0" wire:navigate>
                    <div class="w-80 flex items-center justify-center group-hover:scale-105 transition duration-300">
                         <img src="{{ asset('images/logo-festa.png') }}" class="w-full h-auto object-contain drop-shadow-2xl" alt="Logo Festa Major">
                    </div>
                </a>
                
                <div class="w-full">
                    {{ $slot }}
                </div>
            </div>
            
            <div class="mt-6 text-center text-xs text-zinc-500 uppercase tracking-widest">
                &copy; 2026 Saint-Cyprien
            </div>
        </div>
        
        @fluxScripts
    </body>
</html>
