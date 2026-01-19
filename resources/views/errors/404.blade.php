<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page non trouvée | Festa Major 2026</title>
    <link href="https://fonts.bunny.net/css?family=playfair-display:700,900|instrument-sans:400,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-950 text-white h-full flex items-center justify-center font-sans antialiased overflow-hidden">
    
    <div class="absolute inset-0 opacity-20 pointer-events-none">
        <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover grayscale">
        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/50 to-transparent"></div>
    </div>

    <div class="relative z-10 text-center px-6 max-w-lg">
        <div class="mb-12 inline-block">
            <img src="{{ asset('images/logo-festa.png') }}" class="w-64 h-auto drop-shadow-[0_0_30px_rgba(202,138,4,0.3)]" alt="Logo">
        </div>

        <h1 class="text-8xl md:text-9xl font-black text-white/10 absolute -top-20 left-1/2 -translate-x-1/2 pointer-events-none select-none">404</h1>
        
        <h2 class="text-4xl md:text-5xl font-black font-heading text-white mb-6">Oups, l'étincelle s'est éteinte.</h2>
        
        <p class="text-zinc-400 text-lg mb-10 leading-relaxed">
            La page que vous cherchez n'existe pas ou a été déplacée durant les festivités.
        </p>

        <a href="{{ route('home') }}" class="inline-flex px-10 py-4 bg-festa-gold text-festa-red-dark font-black uppercase tracking-widest text-sm rounded-full shadow-[0_0_30px_rgba(202,138,4,0.2)] hover:scale-105 transition transform duration-300">
            Retour à la fête
        </a>
    </div>

    <div class="absolute bottom-10 left-0 right-0 text-center text-zinc-600 text-[10px] uppercase tracking-widest">
        &copy; 2026 Saint-Cyprien
    </div>
</body>
</html>
