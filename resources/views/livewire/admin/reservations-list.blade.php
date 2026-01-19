<?php

use Livewire\Volt\Component;
use App\Models\Order;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

new class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // Modal Detailed View
    public $selectedOrder = null;
    public bool $showDetailModal = false;

    public function with(): array
    {
        $orders = Order::query()
            ->with(['user', 'reservations.product'])
            ->when($this->search, fn (Builder $query) => $query->where(function($q) {
                $q->whereHas('user', fn ($uq) => 
                    $uq->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                )
                ->orWhere('reference', 'like', '%' . $this->search . '%');
            }))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return [
            'orders' => $orders,
        ];
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleStatus(Order $order)
    {
        if ($order->status === 'confirmed') {
            $order->status = 'ready';
            $msg = 'Commande marquée comme prête pour le retrait.';
            
            Notification::create([
                'user_id' => $order->user_id,
                'title' => 'Votre commande est prête ! 🎁',
                'message' => "Bonne nouvelle ! Votre commande {$order->reference} a été préparée par notre équipe. Vous pouvez venir la récupérer au stand boutique.",
                'type' => 'info'
            ]);
        } elseif ($order->status === 'ready') {
            $order->status = 'paid';
            $msg = 'Commande marquée comme payée et récupérée.';
            
            Notification::create([
                'user_id' => $order->user_id,
                'title' => 'Commande récupérée ! ✅',
                'message' => "Merci de votre achat ! Votre commande {$order->reference} est maintenant finalisée. Profitez bien de la fête !",
                'type' => 'success'
            ]);
        } else {
            $order->status = 'confirmed';
            $msg = 'Commande remise en attente.';
        }

        $order->save();
        $this->dispatch('order-updated', message: $msg);
        
        if ($this->selectedOrder && $this->selectedOrder->id === $order->id) {
            $this->selectedOrder = $order->fresh(['user', 'reservations.product']);
        }
    }

    public function showDetails(Order $order)
    {
        $this->selectedOrder = $order->load(['user', 'reservations.product']);
        $this->showDetailModal = true;
    }

    public function delete($id)
    {
        Order::find($id)?->delete();
        $this->dispatch('order-updated', message: 'Commande supprimée.');
        $this->showDetailModal = false;
    }

    public function export()
    {
        $orders = Order::with(['user', 'reservations.product'])->get();
        
        $csvHeader = ['Référence', 'Date', 'Client', 'Email', 'Articles', 'Total', 'Statut'];
        $csvData = [];

        foreach ($orders as $order) {
            $items = $order->reservations->map(fn($r) => $r->product->name . ($r->size ? " ({$r->size})" : ""))->join(' | ');
            $total = $order->reservations->sum(fn($r) => $r->product->price);
            
            $csvData[] = [
                $order->reference,
                $order->created_at->format('d/m/Y H:i'),
                $order->user->name,
                $order->user->email,
                $items,
                number_format($total, 2) . '€',
                $order->status === 'paid' ? 'Payé' : 'En attente'
            ];
        }

        $filename = "reservations-festa-major-" . now()->format('d-m-Y') . ".csv";
        
        return response()->streamDownload(function () use ($csvHeader, $csvData) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, $csvHeader, ';');
            foreach ($csvData as $row) {
                fputcsv($handle, $row, ';');
            }
            fclose($handle);
        }, $filename);
    }
}; ?>

<div class="space-y-6">
    <!-- Notifications -->
    <div 
        x-data="{ show: false, message: '' }"
        x-on:order-updated.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition
        class="fixed top-24 right-6 z-[110] bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3"
        style="display: none;"
    >
        <flux:icon.check-circle class="size-6 text-white" />
        <p class="text-sm font-bold" x-text="message"></p>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Liste des Commandes</h2>
        
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <flux:button icon="document-arrow-down" variant="ghost" wire:click="export">Exporter Excel</flux:button>
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Rechercher..." class="flex-1 sm:max-w-sm" />
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                         <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Statut / Réf</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-300" wire:click="sortBy('created_at')">
                            Date
                            @if($sortField === 'created_at') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">Client</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider text-right">Total</th>
                        <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition cursor-pointer" wire:click="showDetails({{ $order->id }})">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-2">
                                    @if($order->status === 'paid')
                                        <span class="w-fit text-[9px] font-black uppercase bg-green-500/10 text-green-500 px-2 py-0.5 rounded-full border border-green-500/20">Récupérée</span>
                                    @elseif($order->status === 'ready')
                                        <span class="w-fit text-[9px] font-black uppercase bg-blue-500/10 text-blue-500 px-2 py-0.5 rounded-full border border-blue-500/20 animate-pulse">Prête au retrait</span>
                                    @else
                                        <span class="w-fit text-[9px] font-black uppercase bg-amber-500/10 text-amber-500 px-2 py-0.5 rounded-full border border-amber-500/20">Réservée</span>
                                    @endif
                                    <span class="text-sm font-mono font-bold text-zinc-900 dark:text-festa-gold">
                                        {{ $order->reference }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <flux:avatar :name="$order->user->name" :avatar="$order->user->avatar_path ? asset('storage/'.$order->user->avatar_path) : null" size="sm" />
                                    <div>
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $order->user->name }}</div>
                                        <div class="text-xs text-zinc-500">{{ $order->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-zinc-900 dark:text-white font-mono">
                                {{ number_format($order->reservations->sum(fn($r) => $r->product->price), 2) }}€
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium" wire:click.stop>
                                <flux:dropdown>
                                    <flux:button icon="ellipsis-horizontal" size="sm" variant="ghost" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye" wire:click="showDetails({{ $order->id }})">Détails</flux:menu.item>
                                        @if($order->status === 'confirmed')
                                            <flux:menu.item icon="gift" wire:click="toggleStatus({{ $order->id }})">Marquer comme Prête</flux:menu.item>
                                        @elseif($order->status === 'ready')
                                            <flux:menu.item icon="check-circle" wire:click="toggleStatus({{ $order->id }})">Marquer comme Payée</flux:menu.item>
                                        @else
                                            <flux:menu.item icon="arrow-path" wire:click="toggleStatus({{ $order->id }})">Remettre en attente</flux:menu.item>
                                        @endif
                                        <flux:menu.item icon="printer" href="{{ route('orders.invoice', $order) }}" target="_blank">Imprimer Facture</flux:menu.item>
                                        <flux:menu.item icon="trash" variant="danger" wire:click="delete({{ $order->id }})" wire:confirm="Supprimer cette commande ?">Supprimer</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-zinc-500">
                                Aucun résultat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>

    <!-- Detailed Order Modal -->
    <flux:modal wire:model="showDetailModal" class="min-w-[25rem] max-w-2xl bg-white dark:bg-zinc-900">
        @if($selectedOrder)
            <div class="space-y-8">
                <!-- Modal Header -->
                <div class="flex justify-between items-start border-b border-zinc-100 dark:border-zinc-800 pb-6">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-2xl font-bold">Commande {{ $selectedOrder->reference }}</h3>
                            @if($selectedOrder->status === 'paid')
                                <span class="text-[10px] font-black uppercase bg-green-500/10 text-green-500 px-2 py-0.5 rounded-full border border-green-500/20">Payé</span>
                            @endif
                        </div>
                        <p class="text-sm text-zinc-500">Passée le {{ $selectedOrder->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <flux:button icon="printer" variant="primary" href="{{ route('orders.invoice', $selectedOrder) }}" target="_blank">Imprimer</flux:button>
                </div>

                <!-- Client Info -->
                <div class="grid md:grid-cols-2 gap-8 bg-zinc-50 dark:bg-zinc-800/50 p-6 rounded-2xl border border-zinc-100 dark:border-zinc-800">
                    <div>
                        <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Client</p>
                        <div class="flex items-center gap-3">
                            <flux:avatar :name="$selectedOrder->user->name" :avatar="$selectedOrder->user->avatar_path ? asset('storage/'.$selectedOrder->user->avatar_path) : null" size="md" />
                            <div>
                                <div class="font-bold text-lg">{{ $selectedOrder->user->name }}</div>
                                <a href="mailto:{{ $selectedOrder->user->email }}" class="text-sm text-festa-gold hover:underline">{{ $selectedOrder->user->email }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col justify-center md:items-end">
                        <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Total de la commande</p>
                        <div class="text-3xl font-black text-zinc-900 dark:text-white font-mono">
                            {{ number_format($selectedOrder->reservations->sum(fn($r) => $r->product->price), 2) }}€
                        </div>
                    </div>
                </div>

                <!-- Products List -->
                <div class="space-y-4">
                    <h4 class="font-bold text-zinc-900 dark:text-white uppercase text-xs tracking-widest">Articles à préparer</h4>
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($selectedOrder->reservations->groupBy(fn($r) => $r->product_id . '_' . $r->size) as $items)
                            @php $it = $items->first(); @endphp
                            <div class="flex items-center gap-6 p-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl group hover:border-festa-gold/50 transition">
                                <div class="size-24 rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-800 shrink-0 border border-zinc-100 dark:border-zinc-800">
                                    <img src="{{ $it->product->image_url }}" alt="" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-1">
                                        <span class="text-2xl font-black text-festa-gold">{{ $items->count() }}x</span>
                                        <h5 class="text-lg font-bold text-zinc-900 dark:text-white truncate">{{ $it->product->name }}</h5>
                                    </div>
                                    @if($it->size)
                                        <div class="inline-flex items-center px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 text-xs font-black rounded-lg border border-zinc-200 dark:border-zinc-700">
                                            TAILLE : {{ $it->size }}
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right font-mono font-bold text-zinc-500">
                                    {{ number_format($it->product->price, 2) }}€ / u.
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex gap-3 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button variant="ghost" class="flex-1" wire:click="$set('showDetailModal', false)">Fermer</flux:button>
                    <flux:button variant="primary" class="flex-1 font-bold" wire:click="toggleStatus({{ $selectedOrder->id }})">
                        @if($selectedOrder->status === 'confirmed') Marquer comme Prête 🎁
                        @elseif($selectedOrder->status === 'ready') Valider le paiement ✅
                        @else Remettre en attente ↩️ @endif
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
