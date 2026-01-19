<x-layouts.app>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Réservations Boutique</h1>
            <p class="text-zinc-500 dark:text-zinc-400 mt-1">Suivi des commandes et des réservations de produits.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="bg-festa-gold/10 text-festa-gold-dark dark:text-festa-gold px-4 py-2 rounded-lg text-sm font-bold border border-festa-gold/20">
                Total: {{ \App\Models\Reservation::count() }} réservations
            </div>
        </div>
    </div>

    <livewire:admin.reservations-list />
</x-layouts.app>
