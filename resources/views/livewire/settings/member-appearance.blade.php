<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="bg-zinc-900/50 border border-white/10 rounded-3xl p-8 relative overflow-hidden backdrop-blur-xl">
    <div class="relative z-10">
        <h3 class="font-heading text-2xl text-white font-bold mb-6 flex items-center gap-3">
            <flux:icon.computer-desktop class="size-6 text-festa-gold" />
            Apparence
        </h3>

        <div class="flex flex-col sm:flex-row gap-4" x-data>
            <button @click="$flux.appearance = 'light'" :class="$flux.appearance === 'light' ? 'border-festa-gold bg-festa-gold/10' : 'border-white/10 bg-black/20 hover:bg-white/5'" class="flex-1 p-4 rounded-xl border transition duration-300 flex items-center justify-center gap-3 group">
                <flux:icon.sun class="size-5 text-zinc-400 group-hover:text-white" />
                <span class="font-bold text-white">Clair</span>
            </button>
            
            <button @click="$flux.appearance = 'dark'" :class="$flux.appearance === 'dark' ? 'border-festa-gold bg-festa-gold/10' : 'border-white/10 bg-black/20 hover:bg-white/5'" class="flex-1 p-4 rounded-xl border transition duration-300 flex items-center justify-center gap-3 group">
                <flux:icon.moon class="size-5 text-zinc-400 group-hover:text-white" />
                <span class="font-bold text-white">Sombre</span>
            </button>

            <button @click="$flux.appearance = 'system'" :class="$flux.appearance === 'system' ? 'border-festa-gold bg-festa-gold/10' : 'border-white/10 bg-black/20 hover:bg-white/5'" class="flex-1 p-4 rounded-xl border transition duration-300 flex items-center justify-center gap-3 group">
                <flux:icon.computer-desktop class="size-5 text-zinc-400 group-hover:text-white" />
                <span class="font-bold text-white">Système</span>
            </button>
        </div>
        <p class="text-zinc-500 text-xs mt-4">* Le thème sombre est recommandé pour l'expérience Festa Major.</p>
    </div>
</div>
