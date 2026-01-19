<?php

use App\Models\Product;
use App\Models\Reservation;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\OrderConfirmed;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.festa')] class extends Component {
    public array $cart = []; // [ cartItemKey => { id, quantity, size, ... } ]
    public array $selectedSizes = []; // [ productId => size ]
    public $showCartModal = false;
    public $showConfirmationModal = false;

    public function mount()
    {
        // Initialize default sizes for products that need them
        foreach(Product::where('has_sizes', true)->get() as $product) {
            $this->selectedSizes[$product->id] = 'M'; // Default size
        }
    }

    public function addToCart(Product $product)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $size = null;
        $stockLimit = $product->stock;

        if ($product->has_sizes) {
            $size = $this->selectedSizes[$product->id] ?? 'M';
            $variant = ProductVariant::where('product_id', $product->id)->where('size', $size)->first();
            $stockLimit = $variant ? $variant->stock : 0;
        }

        // Check stock before adding
        if ($stockLimit <= 0) {
            $msg = $product->has_sizes ? "Désolé, la taille {$size} pour {$product->name} est épuisée." : "Désolé, {$product->name} est épuisé.";
            $this->dispatch('cart-error', message: $msg);
            return;
        }

        // Create a unique key for the cart item based on ID and Size
        $cartKey = $product->id . '_' . ($size ?? 'std');

        if (isset($this->cart[$cartKey])) {
            // Check if adding more exceeds stock
            if ($this->cart[$cartKey]['quantity'] >= $stockLimit) {
                $this->dispatch('cart-error', message: "Stock maximum atteint pour cet article.");
                return;
            }
            $this->cart[$cartKey]['quantity']++;
        } else {
            $this->cart[$cartKey] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image_url' => $product->image_url,
                'size' => $size,
                'quantity' => 1,
            ];
        }

        $this->dispatch('cart-updated', message: "{$product->name} ajouté au panier");
    }

    public function increment($cartKey)
    {
        if (isset($this->cart[$cartKey])) {
            $item = $this->cart[$cartKey];
            $product = Product::find($item['id']);
            
            $stockLimit = $product->stock;
            if ($product->has_sizes) {
                $variant = ProductVariant::where('product_id', $product->id)->where('size', $item['size'])->first();
                $stockLimit = $variant ? $variant->stock : 0;
            }

            if ($item['quantity'] < $stockLimit) {
                $this->cart[$cartKey]['quantity']++;
            } else {
                $this->dispatch('cart-error', message: "Stock maximum atteint.");
            }
        }
    }

    public function decrement($cartKey)
    {
        if (isset($this->cart[$cartKey])) {
            if ($this->cart[$cartKey]['quantity'] > 1) {
                $this->cart[$cartKey]['quantity']--;
            } else {
                $this->removeFromCart($cartKey);
            }
        }
    }

    public function removeFromCart($cartKey)
    {
        unset($this->cart[$cartKey]);
        if (empty($this->cart)) {
            $this->showCartModal = false;
        }
    }

    public function confirmOrder()
    {
        if (empty($this->cart)) return;

        // Final stock check before processing
        foreach ($this->cart as $item) {
            $product = Product::find($item['id']);
            $stockLimit = $product->stock;
            if ($product->has_sizes) {
                $variant = ProductVariant::where('product_id', $product->id)->where('size', $item['size'])->first();
                $stockLimit = $variant ? $variant->stock : 0;
            }

            if (!$product || $stockLimit < $item['quantity']) {
                $this->dispatch('cart-error', message: "Le stock a changé pour {$item['name']}. Veuillez vérifier votre panier.");
                return;
            }
        }

        $reference = '#FESTA-' . strtoupper(Str::random(8));

        $order = Order::create([
            'user_id' => Auth::id(),
            'reference' => $reference,
        ]);

        foreach ($this->cart as $item) {
            $product = Product::find($item['id']);
            
            for ($i = 0; $i < $item['quantity']; $i++) {
                Reservation::create([
                    'user_id' => Auth::id(),
                    'product_id' => $item['id'],
                    'order_id' => $order->id,
                    'size' => $item['size'],
                ]);
            }

            // Decrement stock
            if ($product->has_sizes) {
                $variant = ProductVariant::where('product_id', $product->id)->where('size', $item['size'])->first();
                $variant->decrement('stock', $item['quantity']);
            } else {
                $product->decrement('stock', $item['quantity']);
            }
        }

        // Send Email
        Mail::to(Auth::user())->send(new OrderConfirmed($order));

        $this->cart = [];
        $this->showConfirmationModal = false;
        $this->showCartModal = false;

        $this->dispatch('reservation-success', message: "Commande confirmée ! Réf: {$reference}");
    }

    public function getTotalPriceProperty()
    {
        $total = 0;
        foreach ($this->cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }
    
    public function getCartCountProperty()
    {
        $count = 0;
        foreach ($this->cart as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }
}; ?>

@php
    // SEO JSON-LD Generation
    $seoProducts = \App\Models\Product::all();
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'itemListElement' => $seoProducts->map(function($product, $index) {
            return [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'Product',
                    'name' => $product->name,
                    'image' => $product->image_url,
                    'description' => $product->description,
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => $product->price,
                        'priceCurrency' => 'EUR',
                        'availability' => $product->stock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                        'url' => route('boutique') // Since we don't have individual pages yet
                    ]
                ]
            ];
        })
    ];
@endphp

@push('head')
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endpush

<div class="py-12">
    <!-- Notifications -->
    <div 
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:cart-updated.window="show = true; message = $event.detail.message; type = 'success'; setTimeout(() => show = false, 3000)"
        x-on:cart-error.window="show = true; message = $event.detail.message; type = 'error'; setTimeout(() => show = false, 4000)"
        x-on:reservation-success.window="show = true; message = $event.detail.message; type = 'success'; setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="fixed top-24 right-6 z-50 px-6 py-4 rounded-xl shadow-2xl flex items-center gap-3"
        :class="type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'"
        style="display: none;"
    >
        <template x-if="type === 'success'">
            <flux:icon.check-circle class="size-6 text-white" />
        </template>
        <template x-if="type === 'error'">
            <flux:icon.exclamation-circle class="size-6 text-white" />
        </template>
        <div>
            <h4 class="font-bold" x-text="type === 'success' ? 'Succès !' : 'Erreur'"></h4>
            <p class="text-sm" x-text="message"></p>
        </div>
    </div>

    <!-- Header Section -->
    <div class="text-center mb-16">
        <h2 class="text-festa-gold text-xs font-black uppercase tracking-[0.4em] mb-4">{{ __('Souvenirs') }}</h2>
        <h1 class="font-heading text-4xl md:text-5xl font-bold text-white mb-4">{{ __('La Boutique Officielle') }}</h1>
        <p class="text-zinc-400 mt-4 max-w-xl mx-auto">
            {{ __('Réservez vos articles souvenirs et billets pour la Festa Major 2026.') }}
        </p>
    </div>

    <!-- Unified Payment Info Banner -->
    <div class="max-w-4xl mx-auto mb-16">
        <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-festa-gold/20 via-festa-gold/40 to-festa-gold/20 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
            <div class="relative bg-zinc-900 border border-festa-gold/30 rounded-2xl p-6 md:p-8 flex flex-col md:flex-row items-center gap-6 text-center md:text-left backdrop-blur-xl">
                <div class="size-16 rounded-full bg-festa-gold/10 flex items-center justify-center shrink-0 border border-festa-gold/20">
                    <flux:icon.banknotes class="size-8 text-festa-gold" />
                </div>
                <div>
                    <h3 class="font-heading text-xl text-white font-bold mb-1">{{ __('Informations de Paiement') }}</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed">
                        {{ __('Pour faciliter votre expérience, les réservations se font en ligne mais le règlement s\'effectue exclusivement sur place, au stand boutique de la Festa Major.') }} {{ __('Règlement en espèces ou par carte bancaire accepté.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Cart Button -->
    @if($this->cartCount > 0)
        <button wire:click="$set('showCartModal', true)" class="fixed bottom-8 right-8 z-40 bg-festa-gold text-festa-red-dark p-4 rounded-full shadow-[0_0_30px_rgba(234,179,8,0.4)] hover:scale-110 transition duration-300 flex items-center gap-2 group animate-bounce-subtle">
            <div class="relative">
                <flux:icon.shopping-bag class="size-8" />
                <span class="absolute -top-2 -right-2 bg-red-600 text-white text-[10px] font-bold size-5 flex items-center justify-center rounded-full border border-white">
                    {{ $this->cartCount }}
                </span>
            </div>
            <span class="font-black uppercase tracking-widest text-sm pr-2 group-hover:block hidden">Voir le panier</span>
        </button>
    @endif

    <!-- Cart Modal -->
    <flux:modal wire:model="showCartModal" class="min-w-[22rem] max-w-lg bg-zinc-900 border border-white/10 text-white">
        <div class="space-y-6">
            <div class="flex items-center gap-3 border-b border-white/10 pb-4">
                <flux:icon.shopping-bag class="size-6 text-festa-gold" />
                <h3 class="font-heading text-2xl font-bold flex-1">Mon Panier</h3>
            </div>

            <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                @foreach($cart as $key => $item)
                    <div class="flex items-center gap-4 bg-white/5 p-3 rounded-xl border border-white/5">
                        <div class="size-16 rounded-lg bg-black/20 overflow-hidden shrink-0">
                            <img src="{{ $item['image_url'] }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-white truncate">{{ $item['name'] }}</h4>
                            @if($item['size'])
                                <p class="text-zinc-400 text-xs mt-0.5">Taille : <span class="text-festa-gold font-bold">{{ $item['size'] }}</span></p>
                            @endif
                            <p class="text-zinc-500 text-xs mt-0.5">{{ number_format($item['price'], 2) }}€ / unité</p>
                        </div>
                        
                        <div class="flex flex-col items-end gap-2">
                            <p class="text-white font-bold">{{ number_format($item['price'] * $item['quantity'], 2) }}€</p>
                            
                            <!-- Quantity Controls -->
                            <div class="flex items-center bg-black/40 rounded-lg border border-white/10 overflow-hidden">
                                <button wire:click="decrement('{{ $key }}')" class="px-2 py-1 hover:bg-white/10 text-zinc-400 hover:text-white transition">
                                    <flux:icon.minus class="size-3" />
                                </button>
                                <span class="px-2 text-xs font-mono font-bold text-white">{{ $item['quantity'] }}</span>
                                <button wire:click="increment('{{ $key }}')" class="px-2 py-1 hover:bg-white/10 text-zinc-400 hover:text-white transition">
                                    <flux:icon.plus class="size-3" />
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-white/10 pt-4 space-y-4">
                <div class="bg-festa-gold/5 border border-festa-gold/20 rounded-xl p-4 text-center">
                    <p class="text-festa-gold text-[10px] font-bold uppercase tracking-widest leading-relaxed">
                        ✨ Rappel : Le paiement se fera directement sur le lieu de la Festa Major lors du retrait.
                    </p>
                </div>

                <div class="flex justify-between items-center mb-2 px-1">
                    <span class="text-zinc-400 uppercase tracking-wider text-xs font-bold">Total à régler sur place</span>
                    <span class="text-2xl font-bold text-white">{{ number_format($this->totalPrice, 2) }}€</span>
                </div>
                
                <flux:button variant="primary" class="w-full bg-festa-gold text-festa-red-dark font-bold uppercase tracking-wider hover:bg-festa-gold-dark py-4 text-base" wire:click="$set('showConfirmationModal', true)">
                    Valider la commande
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Final Confirmation Modal -->
    <flux:modal wire:model="showConfirmationModal" class="min-w-[22rem] max-w-md bg-zinc-900 border border-white/10 text-white">
        <div class="space-y-6 text-center">
            <div class="mx-auto size-16 bg-festa-gold/10 rounded-full flex items-center justify-center mb-4">
                <flux:icon.check-circle class="size-8 text-festa-gold" />
            </div>
            <h3 class="font-heading text-2xl font-bold mb-2">Confirmation</h3>
            <p class="text-zinc-400 text-sm">
                Une référence unique sera générée pour l'ensemble de ces {{ $this->cartCount }} articles.
            </p>

            <div class="space-y-4 bg-white/5 rounded-xl p-4 border border-white/10 text-left">
                <div class="flex justify-between items-center py-2 border-b border-white/5">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Nom</span>
                    <span class="font-bold text-white">{{ Auth::user()?->name }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">Email</span>
                    <span class="font-bold text-white">{{ Auth::user()?->email }}</span>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <flux:button variant="ghost" class="flex-1 hover:bg-white/10 text-zinc-400 hover:text-white" wire:click="$set('showConfirmationModal', false)">Retour</flux:button>
                <flux:button variant="primary" class="flex-1 bg-green-500 text-white font-bold uppercase tracking-wider hover:bg-green-600" wire:click="confirmOrder">
                    Confirmer
                </flux:button>
            </div>
        </div>
    </flux:modal>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach(Product::with('variants')->get() as $product)
            <div wire:key="product-{{ $product->id }}" class="group relative bg-white/5 border border-white/10 rounded-2xl overflow-hidden hover:border-festa-gold/30 transition duration-500 flex flex-col h-full">
                <div class="aspect-square overflow-hidden bg-black/20 relative">
                    <img src="{{ $product->image_url }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110" alt="{{ $product->name }}">
                    
                    @php
                        $isOutOfStock = false;
                        $stockLabel = '';
                        if ($product->has_sizes) {
                            $selectedSize = $selectedSizes[$product->id] ?? 'M';
                            $v = $product->variants->where('size', $selectedSize)->first();
                            $currentStock = $v ? $v->stock : 0;
                            $isOutOfStock = $currentStock <= 0;
                            $stockLabel = $currentStock > 0 ? "{$currentStock} en taille {$selectedSize}" : "Taille {$selectedSize} épuisée";
                        } else {
                            $isOutOfStock = $product->stock <= 0;
                            $stockLabel = $product->stock > 0 ? "{$product->stock} disponibles" : "Épuisé";
                        }
                    @endphp

                    @if(!$isOutOfStock && ($product->has_sizes ? $currentStock : $product->stock) <= 10)
                        <div class="absolute top-4 left-4 bg-amber-500 text-white text-[8px] font-black px-2 py-1 rounded-full uppercase tracking-widest shadow-lg animate-pulse">
                            Plus que {{ $product->has_sizes ? $currentStock : $product->stock }} !
                        </div>
                    @endif
                    
                    @if($isOutOfStock)
                        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-10">
                            <span class="px-4 py-2 bg-red-600 text-white font-black uppercase tracking-widest text-xs rounded-lg transform -rotate-12 border-2 border-white shadow-2xl">
                                Rupture de stock
                            </span>
                        </div>
                    @endif

                    @if($product->price > 15 && !$isOutOfStock)
                        <div class="absolute top-4 right-4 bg-festa-red text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            Populaire
                        </div>
                    @endif
                </div>
                <div class="p-6 flex flex-col flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-heading text-xl text-white font-bold">{{ $product->name }}</h3>
                        <span class="text-festa-gold font-bold text-xl ml-4 shrink-0">{{ number_format($product->price, 2) }}€</span>
                    </div>
                    
                    <p class="text-zinc-400 text-sm leading-relaxed mb-6" title="{{ $product->description }}">{{ $product->description }}</p>
                    
                    <!-- Stock display -->
                    <div class="mb-6 flex items-center gap-2">
                        <div class="flex-1 h-1 bg-white/5 rounded-full overflow-hidden">
                            @php 
                                $percent = $product->has_sizes 
                                    ? ($v ? min(100, $v->stock * 5) : 0) 
                                    : min(100, $product->stock);
                            @endphp
                            <div class="h-full transition-all duration-1000 {{ $percent > 20 ? 'bg-green-500' : ($percent > 0 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ $percent }}%"></div>
                        </div>
                        <span class="text-[10px] font-bold {{ !$isOutOfStock ? 'text-zinc-500' : 'text-red-500' }} uppercase tracking-tighter whitespace-nowrap">
                            @if($isOutOfStock)
                                {{ __('Épuisé') }}
                            @else
                                {{ $product->has_sizes ? $currentStock . ' ' . __('en') . ' ' . __('taille') . ' ' . $selectedSize : $product->stock . ' ' . __('disponibles') }}
                            @endif
                        </span>
                    </div>

                    <div class="mt-auto">
                        <!-- Size Selector for applicable products -->
                        @if($product->has_sizes && $product->stock > 0)
                            <div class="mb-6">
                                <label class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-3 block">{{ __('Sélectionner une taille') }}</label>
                                <div class="grid grid-cols-5 gap-2">
                                    @foreach(['S', 'M', 'L', 'XL', 'XXL'] as $size)
                                        <button 
                                            wire:click="$set('selectedSizes.{{ $product->id }}', '{{ $size }}')" 
                                            class="aspect-square flex items-center justify-center rounded-lg border text-xs font-bold transition duration-200
                                            {{ ($selectedSizes[$product->id] ?? 'M') === $size 
                                                ? 'bg-festa-gold text-festa-red-dark border-festa-gold' 
                                                : 'bg-black/20 text-zinc-400 border-white/10 hover:bg-white/10 hover:text-white' 
                                            }}"
                                        >
                                            {{ $size }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @auth
                           @if(!$isOutOfStock)
                               <button wire:click="addToCart({{ $product->id }})" class="w-full py-3 bg-white text-zinc-900 font-black uppercase tracking-widest text-xs rounded-xl hover:bg-festa-gold hover:text-festa-red-dark transition duration-300 relative overflow-hidden flex items-center justify-center gap-2 group/btn">
                                    <flux:icon.plus class="size-4 group-hover/btn:rotate-90 transition" />
                                    {{ __('Ajouter au panier') }}
                                </button>
                           @else
                               <button disabled class="w-full py-3 bg-zinc-800 text-zinc-600 font-black uppercase tracking-widest text-xs rounded-xl cursor-not-allowed border border-white/5">
                                    Stock épuisé
                                </button>
                           @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full text-center py-3 bg-zinc-800 text-zinc-400 font-black uppercase tracking-widest text-xs rounded-xl hover:bg-zinc-700 hover:text-white transition duration-300">
                                Connexion requise
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 10px; }
        @keyframes bounce-subtle {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }
        .animate-bounce-subtle { animation: bounce-subtle 3s infinite ease-in-out; }
    </style>
</div>
