<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Order;
use App\Models\Visit;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

new class extends Component {
    public function with(): array
    {
        // KPI 1: Total Users & Growth (Last 30 days)
        $totalUsers = User::count();
        $newUsersLast30Days = User::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $userGrowth = $totalUsers > 0 ? ($newUsersLast30Days / $totalUsers) * 100 : 0;

        // KPI 2: Total Reservations & Revenue
        $totalReservations = Order::count();
        $reservationsLast30Days = Order::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        
        // Revenue calculation optimized via SQL JOIN
        $potentialRevenue = Reservation::join('products', 'reservations.product_id', '=', 'products.id')
            ->sum('products.price');
        
        // KPI 3: Visits
        $totalVisits = Visit::count();
        $uniqueVisitors = Visit::distinct('ip_address')->count();

        // Top Products (Top 2)
        $topProducts = Reservation::select('product_id', DB::raw('count(*) as total'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->take(2)
            ->get();

        // Recent Activity
        $recentReservations = Order::with(['user', 'reservations.product'])
            ->latest()
            ->take(6)
            ->get();

        // Chart Data (Last 7 days)
        $chartData = [];
        $days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('d/m');
            $chartData['users'][] = User::whereDate('created_at', $date->toDateString())->count();
            $chartData['orders'][] = Order::whereDate('created_at', $date->toDateString())->count();
        }

        return [
            'totalUsers' => $totalUsers,
            'newUsersLast30Days' => $newUsersLast30Days,
            'userGrowth' => $userGrowth,
            'totalReservations' => $totalReservations,
            'reservationsLast30Days' => $reservationsLast30Days,
            'potentialRevenue' => $potentialRevenue,
            'totalVisits' => $totalVisits,
            'uniqueVisitors' => $uniqueVisitors,
            'topProducts' => $topProducts,
            'recentReservations' => $recentReservations,
            'chartDays' => $days,
            'chartUsers' => $chartData['users'],
            'chartOrders' => $chartData['orders'],
        ];
    }
}; ?>

<div class="space-y-8" x-data="{
    initCharts() {
        if (typeof ApexCharts === 'undefined') return;
        
        const baseOptions = {
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false },
                background: 'transparent',
                fontFamily: 'Instrument Sans, sans-serif',
                sparkline: { enabled: false }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            grid: {
                borderColor: '#27272a',
                strokeDashArray: 4,
                padding: { left: 10, right: 10 }
            },
            xaxis: {
                categories: {{ json_encode($chartDays) }},
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: '#71717a', fontWeight: 600 } }
            },
            yaxis: {
                show: false
            },
            tooltip: {
                theme: 'dark',
                x: { show: false },
                style: { fontSize: '12px' }
            }
        };

        // User Chart
        new ApexCharts(document.querySelector('#userChart'), {
            ...baseOptions,
            series: [{ name: 'Inscriptions', data: {{ json_encode($chartUsers) }} }],
            colors: ['#EF4444'],
            fill: {
                type: 'gradient',
                gradient: { opacityFrom: 0.4, opacityTo: 0.05 }
            }
        }).render();

        // Order Chart
        new ApexCharts(document.querySelector('#orderChart'), {
            ...baseOptions,
            series: [{ name: 'Commandes', data: {{ json_encode($chartOrders) }} }],
            colors: ['#FACC15'],
            fill: {
                type: 'gradient',
                gradient: { opacityFrom: 0.4, opacityTo: 0.05 }
            }
        }).render();
    }
}" x-init="setTimeout(() => initCharts(), 100)">
    <!-- Chart Library CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue Card -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 relative overflow-hidden group shadow-sm">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition transform group-hover:scale-110">
                <flux:icon.banknotes class="size-16 text-festa-gold" />
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">CA Potentiel</h3>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($potentialRevenue, 2) }}€</span>
                </div>
                <p class="mt-2 text-xs text-festa-gold font-medium italic">Paiement sur place</p>
            </div>
        </div>

        <!-- Reservations Card -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 relative overflow-hidden group shadow-sm">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition transform group-hover:scale-110">
                <flux:icon.shopping-bag class="size-16 text-festa-red" />
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Commandes</h3>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $totalReservations }}</span>
                    @if($reservationsLast30Days > 0)
                        <span class="text-xs font-bold text-green-500 bg-green-500/10 px-1.5 py-0.5 rounded">+{{ $reservationsLast30Days }} ce mois</span>
                    @endif
                </div>
                <p class="mt-2 text-xs text-zinc-500">Total des réservations</p>
            </div>
        </div>

        <!-- Members Card -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 relative overflow-hidden group shadow-sm">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition transform group-hover:scale-110">
                <flux:icon.users class="size-16 text-blue-500" />
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Membres</h3>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $totalUsers }}</span>
                    @if($newUsersLast30Days > 0)
                        <span class="text-xs font-bold text-blue-500 bg-blue-500/10 px-1.5 py-0.5 rounded">+{{ $newUsersLast30Days }} récents</span>
                    @endif
                </div>
                <p class="mt-2 text-xs text-zinc-500">Utilisateurs inscrits</p>
            </div>
        </div>

        <!-- Visits Card -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 relative overflow-hidden group shadow-sm">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition transform group-hover:scale-110">
                <flux:icon.globe-alt class="size-16 text-purple-500" />
            </div>
            <div class="relative z-10">
                <h3 class="text-sm font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Visites</h3>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-zinc-900 dark:text-white">{{ $totalVisits }}</span>
                </div>
                <p class="mt-2 text-xs text-zinc-500">{{ $uniqueVisitors }} visiteurs uniques</p>
            </div>
        </div>
    </div>

    <!-- Performance Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- User Growth Chart -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        <flux:icon.users class="size-5 text-festa-red" />
                        Flux d'Inscriptions
                    </h3>
                    <p class="text-[10px] text-zinc-500 uppercase font-black tracking-widest mt-1">7 derniers jours</p>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-bold text-white">{{ array_sum($chartUsers) }}</span>
                    <span class="block text-[8px] text-zinc-500 uppercase font-bold text-right">Nouveaux</span>
                </div>
            </div>
            <div id="userChart" class="w-full"></div>
        </div>

        <!-- Order Growth Chart -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                        <flux:icon.shopping-bag class="size-5 text-festa-gold" />
                        Volume de Commandes
                    </h3>
                    <p class="text-[10px] text-zinc-500 uppercase font-black tracking-widest mt-1">7 derniers jours</p>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-bold text-white">{{ array_sum($chartOrders) }}</span>
                    <span class="block text-[8px] text-zinc-500 uppercase font-bold text-right">Réservations</span>
                </div>
            </div>
            <div id="orderChart" class="w-full"></div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Top Products -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden flex flex-col shadow-sm">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <h3 class="font-bold text-zinc-900 dark:text-white">🏆 Top Ventes</h3>
                <flux:button size="sm" variant="ghost" href="{{ route('admin.reservations') }}">Voir tout</flux:button>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse($topProducts as $item)
                    <div class="p-4 flex items-center gap-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                        <div class="size-12 rounded-lg bg-zinc-100 dark:bg-zinc-800 overflow-hidden shrink-0">
                            <img src="{{ $item->product->image_url }}" alt="" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-white truncate">{{ $item->product->name }}</h4>
                            <p class="text-xs text-zinc-500">{{ number_format($item->product->price, 2) }}€ / unité</p>
                        </div>
                        <div class="text-right">
                            <span class="block text-lg font-bold text-festa-gold">{{ $item->total }}</span>
                            <span class="text-[10px] uppercase text-zinc-500">Réservés</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-zinc-500 text-sm">
                        Aucune vente pour le moment.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden flex flex-col shadow-sm">
             <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <h3 class="font-bold text-zinc-900 dark:text-white">⏱️ Activité Récente</h3>
                <flux:button size="sm" variant="ghost" href="{{ route('admin.reservations') }}">Gérer</flux:button>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse($recentReservations as $order)
                    <div class="p-4 flex items-center gap-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition">
                         <flux:avatar :name="$order->user->name" :avatar="$order->user->avatar_path ? asset('storage/'.$order->user->avatar_path) : null" size="sm" />
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between">
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-white truncate">{{ $order->user->name }}</h4>
                                <span class="text-xs font-mono text-zinc-400">{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-zinc-500 truncate">
                                Commande <span class="text-festa-gold font-bold">{{ $order->reference }}</span> ({{ $order->reservations->count() }} articles)
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-zinc-500 text-sm">
                        Aucune activité récente.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
