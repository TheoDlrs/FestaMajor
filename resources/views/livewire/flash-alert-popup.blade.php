<?php

use Livewire\Volt\Component;
use App\Models\FlashAlert;

new class extends Component {
    public $alert;
    public $isOpen = false;

    public function mount()
    {
        $this->alert = FlashAlert::first();

        // Si une alerte existe, est active, et n'a pas été fermée dans cette session
        if ($this->alert && $this->alert->is_active && !session()->has('flash_alert_dismissed_' . $this->alert->updated_at->timestamp)) {
            $this->isOpen = true;
        }
    }

    public function close()
    {
        $this->isOpen = false;
        // On mémorise que l'utilisateur a fermé CETTE version de l'alerte
        session()->put('flash_alert_dismissed_' . $this->alert->updated_at->timestamp, true);
    }
}; ?>

<div x-data="{ open: @entangle('isOpen') }" 
     x-show="open"
     class="fixed inset-0 z-[100] flex items-center justify-center px-4"
     style="display: none;">

    <!-- Backdrop Overlay -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute inset-0 bg-black/60 backdrop-blur-sm"
         wire:click="close">
    </div>

    @if($alert)
        <div x-show="open"
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 translate-y-10 scale-90"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-10 scale-90"
             class="pointer-events-auto w-full max-w-lg bg-zinc-900 border border-white/10 rounded-2xl shadow-[0_0_50px_rgba(0,0,0,0.5)] overflow-hidden relative group z-10">
            
            <!-- Type Color Indicator (Top Bar) -->
            <div class="absolute top-0 left-0 w-full h-1 
                {{ $alert->type === 'danger' ? 'bg-red-500' : ($alert->type === 'warning' ? 'bg-orange-500' : 'bg-blue-500') }}">
            </div>

            <div class="p-8 relative text-center">
                <!-- Icon Badge -->
                <div class="mx-auto mb-6 inline-flex p-4 rounded-full border-4 border-zinc-900 shadow-xl relative z-10 
                    {{ $alert->type === 'danger' ? 'bg-red-500 text-white' : ($alert->type === 'warning' ? 'bg-orange-500 text-white' : 'bg-blue-500 text-white') }}">
                    <flux:icon.megaphone class="size-8 animate-pulse" />
                </div>

                <h3 class="text-white font-heading font-black text-3xl mb-4 uppercase tracking-wide">{{ $alert->title }}</h3>
                
                <p class="text-zinc-300 text-lg mb-8 leading-relaxed">
                    {{ $alert->message }}
                </p>

                <div class="flex flex-col gap-3">
                    @if($alert->button_text)
                        <a href="{{ $alert->button_url }}" wire:click="close" class="w-full py-4 rounded-xl font-black text-sm uppercase tracking-widest transition shadow-lg hover:scale-[1.02] active:scale-[0.98]
                            {{ $alert->type === 'danger' ? 'bg-red-600 text-white hover:bg-red-700 shadow-red-900/20' : ($alert->type === 'warning' ? 'bg-orange-500 text-white hover:bg-orange-600 shadow-orange-900/20' : 'bg-blue-600 text-white hover:bg-blue-700 shadow-blue-900/20') }}">
                            {{ $alert->button_text }}
                        </a>
                    @endif
                    
                    <button wire:click="close" class="text-zinc-500 text-xs font-bold uppercase tracking-widest hover:text-white transition py-2">
                        {{ __('Fermer et continuer') }}
                    </button>
                </div>
            </div>
            
            <!-- Background Glow Effect -->
            <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full blur-[80px] opacity-10 pointer-events-none
                {{ $alert->type === 'danger' ? 'bg-red-500' : ($alert->type === 'warning' ? 'bg-orange-500' : 'bg-blue-500') }}">
            </div>
             <div class="absolute -left-20 -bottom-20 w-64 h-64 rounded-full blur-[80px] opacity-10 pointer-events-none
                {{ $alert->type === 'danger' ? 'bg-red-500' : ($alert->type === 'warning' ? 'bg-orange-500' : 'bg-blue-500') }}">
            </div>
        </div>
    @endif
</div>

