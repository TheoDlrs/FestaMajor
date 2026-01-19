<x-layouts.app>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Vue d'ensemble</h1>
        <p class="text-zinc-500 dark:text-zinc-400 mt-1">
            Bienvenue sur le tableau de bord administrateur de la Festa Major.
        </p>
    </div>

    <div class="mb-12">
        <livewire:admin.flash-alert-manager />
    </div>

    <livewire:admin.dashboard-stats />
</x-layouts.app>