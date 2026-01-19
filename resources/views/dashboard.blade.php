<x-layouts.festa>
    <div class="space-y-12 animate-reveal opacity-0" style="animation-delay: 100ms">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-6 border-b border-white/10 pb-8">
            <div class="flex flex-col md:flex-row items-center gap-6 text-center md:text-left">
                <!-- User Avatar -->
                <div class="shrink-0">
                    <div class="size-20 rounded-2xl overflow-hidden border-2 border-festa-gold/30 shadow-2xl bg-zinc-900">
                        @if(auth()->user()->avatar_path)
                            <img src="{{ asset('storage/' . auth()->user()->avatar_path) }}" class="w-full h-full object-cover">
                        @else
                            <flux:avatar :name="auth()->user()->name" class="w-full h-full text-2xl" />
                        @endif
                    </div>
                </div>
                <div>
                    <h2 class="text-festa-gold text-xs font-black uppercase tracking-[0.4em] mb-2">{{ __('Espace Membre') }}</h2>
                    <h1 class="font-heading text-4xl md:text-5xl font-bold text-white">
                        Hola, <span class="text-transparent bg-clip-text bg-gradient-to-r from-festa-red to-festa-gold">{{ auth()->user()->name }}</span> !
                    </h1>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                 <!-- Notification Center -->
                 <livewire:partials.notification-center />

                 <a href="{{ route('boutique') }}" class="group px-6 py-3 bg-festa-red text-white font-bold uppercase tracking-widest text-xs rounded-full shadow-[0_0_20px_rgba(206,17,38,0.3)] hover:shadow-[0_0_30px_rgba(206,17,38,0.5)] hover:bg-festa-red-dark transition duration-300">
                    <span class="flex items-center gap-2">
                        <svg class="size-4 group-hover:rotate-12 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        {{ __('Visiter la Boutique') }}
                    </span>
                </a>
                
                <!-- Security Link -->
                 <a href="{{ route('settings.security') }}" class="px-6 py-3 bg-zinc-800 text-white font-bold uppercase tracking-widest text-xs rounded-full hover:bg-zinc-700 transition duration-300 border border-white/10">
                    <span class="flex items-center gap-2">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        {{ __('Sécurité') }}
                    </span>
                </a>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-white/10 text-white font-bold uppercase tracking-widest text-xs rounded-full border border-white/20 shadow-[0_0_20px_rgba(255,255,255,0.05)] hover:bg-red-500 hover:border-red-500 hover:shadow-[0_0_30px_rgba(239,68,68,0.4)] transition duration-300">
                        {{ __('Déconnexion') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-12">
            
            <!-- Profile Section -->
            <div class="space-y-6">
                <!-- We embed the themed profile component here -->
                <livewire:settings.profile-member />
            </div>

            <!-- Orders/Reservations Section -->
            <div class="space-y-8">
                <div class="text-center md:text-left">
                    <h3 class="font-heading text-3xl text-white font-bold mb-2">{{ __('Mes Commandes') }}</h3>
                    <p class="text-zinc-400">{{ __('Suivi de vos réservations pour la boutique.') }}</p>
                </div>

                <div class="space-y-6 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    @php
                        $orders = auth()->user()->orders()->with(['reservations.product'])->latest()->get();
                    @endphp

                    @if($orders->isEmpty())
                        <div class="bg-zinc-900/50 border border-white/10 rounded-3xl p-12 text-center backdrop-blur-xl">
                            <div class="p-6 bg-white/5 rounded-full mb-6 mx-auto w-fit">
                                <svg class="size-12 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            </div>
                            <h4 class="text-white font-bold text-lg mb-2">{{ __('Aucune commande') }}</h4>
                            <p class="text-zinc-500 text-sm mb-6 max-w-xs mx-auto">{{ __('Vous n\'avez pas encore réservé d\'articles.') }}</p>
                            <a href="{{ route('boutique') }}" class="text-festa-gold text-xs font-black uppercase tracking-widest hover:underline">{{ __('Accéder à la boutique') }}</a>
                        </div>
                    @else
                        @foreach($orders as $order)
                            <div class="bg-zinc-900/50 border border-white/10 rounded-3xl overflow-hidden backdrop-blur-xl">
                                <!-- Order Header -->
                                <div class="bg-white/5 px-6 py-4 flex justify-between items-center border-b border-white/10">
                                    <div class="flex items-center gap-4">
                                        <!-- QR Code Logic -->
                                        <div x-data="{ open: false }" class="shrink-0">
                                            <button @click="open = true" class="flex items-center gap-2 px-3 py-2 bg-white text-black rounded-xl hover:scale-105 transition shadow-lg group">
                                                <flux:icon.qr-code class="size-5" />
                                                <span class="text-[10px] font-black uppercase tracking-widest">{{ __('Pass QR') }}</span>
                                            </button>

                                            <!-- Full Screen QR Modal -->
                                            <div x-show="open" 
                                                 x-transition
                                                 class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-zinc-950/95 backdrop-blur-xl"
                                                 style="display: none;">
                                                <div @click.away="open = false" class="max-w-sm w-full bg-zinc-900 border border-white/10 rounded-[2.5rem] p-8 text-center shadow-2xl relative">
                                                    <button @click="open = false" class="absolute top-6 right-6 text-zinc-500 hover:text-white transition">
                                                        <flux:icon.x-mark class="size-8" />
                                                    </button>

                                                    <div class="mb-8">
                                                        <h3 class="font-heading text-2xl font-bold text-white mb-2">{{ __('Votre Pass Retrait') }}</h3>
                                                        <p class="text-zinc-500 text-xs uppercase tracking-widest font-bold">{{ __('Réf:') }} {{ $order->reference }}</p>
                                                    </div>

                                                    <div class="bg-white p-6 rounded-3xl shadow-2xl mb-8 aspect-square flex items-center justify-center overflow-hidden">
                                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode(route('admin.reservations', ['search' => $order->reference])) }}" class="w-full h-full" alt="QR Code Large">
                                                    </div>

                                                    <p class="text-zinc-400 text-sm leading-relaxed mb-6">
                                                        {{ __('Présentez ce QR Code au stand boutique pour récupérer vos articles.') }}
                                                    </p>

                                                    <flux:button @click="open = false" variant="primary" class="w-full bg-festa-gold text-black font-black uppercase tracking-widest py-4 rounded-2xl shadow-xl">
                                                        {{ __('Fermer') }}
                                                    </flux:button>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <div class="flex items-center gap-3">
                                                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">{{ __('Réf:') }}</span>
                                                <span class="text-sm font-mono font-bold text-festa-gold">{{ $order->reference }}</span>
                                            </div>
                                            <span class="text-[10px] font-bold text-zinc-500 uppercase">{{ $order->created_at->format('d/m/Y à H:i') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <form method="POST" action="{{ route('orders.destroy', $order) }}" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir annuler cette commande ?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500/50 hover:text-red-500 transition">
                                                <flux:icon.trash class="size-4" />
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Order Progress Timeline -->
                                <div class="px-6 py-6 border-b border-white/5 bg-black/20">
                                    <div class="relative">
                                        <!-- Line -->
                                        <div class="absolute top-1/2 left-0 w-full h-0.5 bg-zinc-800 -translate-y-1/2"></div>
                                        <div class="absolute top-1/2 left-0 h-0.5 bg-gradient-to-r from-festa-red to-festa-gold -translate-y-1/2 transition-all duration-1000" 
                                             style="width: {{ $order->status === 'paid' ? '100%' : ($order->status === 'ready' ? '50%' : '0%') }}"></div>
                                        
                                        <!-- Dots -->
                                        <div class="relative flex justify-between">
                                            <!-- Step 1: Confirmed -->
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="size-4 rounded-full border-2 border-festa-red bg-festa-red shadow-[0_0_10px_rgba(239,68,68,0.5)] z-10"></div>
                                                <span class="text-[8px] font-black uppercase tracking-widest text-festa-red">{{ __('Réservée') }}</span>
                                            </div>
                                            
                                            <!-- Step 2: Ready -->
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="size-4 rounded-full border-2 transition-all duration-500 z-10 
                                                    {{ in_array($order->status, ['ready', 'paid']) ? 'border-festa-gold bg-festa-gold shadow-[0_0_10px_rgba(234,179,8,0.5)]' : 'border-zinc-800 bg-zinc-900' }}"></div>
                                                <span class="text-[8px] font-black uppercase tracking-widest {{ in_array($order->status, ['ready', 'paid']) ? 'text-festa-gold' : 'text-zinc-600' }}">{{ __('Prête') }}</span>
                                            </div>

                                            <!-- Step 3: Paid -->
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="size-4 rounded-full border-2 transition-all duration-500 z-10 
                                                    {{ $order->status === 'paid' ? 'border-green-500 bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]' : 'border-zinc-800 bg-zinc-900' }}"></div>
                                                <span class="text-[8px] font-black uppercase tracking-widest {{ $order->status === 'paid' ? 'text-green-500' : 'text-zinc-600' }}">{{ __('Récupérée') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Items -->
                                <div class="p-6 space-y-4">
                                    @foreach($order->reservations->groupBy(fn($r) => $r->product_id . '_' . $r->size) as $items)
                                        @php $item = $items->first(); @endphp
                                        <div class="flex items-center gap-4">
                                            <div class="size-12 rounded-lg overflow-hidden bg-zinc-800 shrink-0 border border-white/5">
                                                <img src="{{ $item->product->image_url }}" alt="" class="w-full h-full object-cover">
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-white">{{ $items->count() }}x</span>
                                                    <h5 class="text-sm text-zinc-300 truncate">{{ $item->product->name }}</h5>
                                                </div>
                                                @if($item->size)
                                                    <p class="text-[10px] text-festa-gold font-bold uppercase tracking-wider mt-0.5">{{ __('Taille :') }} {{ $item->size }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs font-bold text-white">{{ number_format($item->product->price * $items->count(), 2) }}€</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Order Footer -->
                                <div class="px-6 py-3 bg-festa-gold/5 flex justify-between items-center border-t border-white/5">
                                    <div class="flex items-center gap-4">
                                        <span class="text-[10px] font-bold text-zinc-400 uppercase">{{ __('Total à payer sur place') }}</span>
                                        <span class="text-base font-bold text-festa-gold">{{ number_format($order->reservations->sum(fn($r) => $r->product->price), 2) }}€</span>
                                    </div>
                                    
                                    <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-white/50 hover:text-white transition group">
                                        <flux:icon.document-arrow-down class="size-4 text-zinc-500 group-hover:text-festa-gold transition" />
                                        {{ __('Facture PDF') }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
    </style>
</x-layouts.festa>