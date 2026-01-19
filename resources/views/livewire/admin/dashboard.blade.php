<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Visit;

new class extends Component {
    public function with(): array
    {
        return [
            'totalUsers' => User::count(),
            'totalReservations' => Reservation::count(),
            'totalVisits' => Visit::count(),
        ];
    }
}; ?>

<div class="p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">Tableau de Bord Administrateur</h1>
        <p class="text-zinc-500 dark:text-zinc-400">Vue d'ensemble des statistiques de la Festa Major.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Users Stat -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <flux:icon.users class="size-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Membres Inscrits</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $totalUsers }}</p>
                </div>
            </div>
        </div>

        <!-- Reservations Stat -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-festa-gold/20 rounded-lg">
                    <flux:icon.shopping-bag class="size-6 text-festa-gold-dark dark:text-festa-gold" />
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Ventes / Réservations</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $totalReservations }}</p>
                </div>
            </div>
        </div>

        <!-- Visits Stat -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <flux:icon.chart-bar class="size-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Visites Totales</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $totalVisits }}</p>
                </div>
            </div>
        </div>
    </div>
</div>