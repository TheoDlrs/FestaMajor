<?php

use Livewire\Volt\Component;
use App\Models\FlashAlert;

new class extends Component {
    public $alert;
    public bool $is_active;
    public string $type;
    public string $title;
    public string $message;
    public string $button_text;
    public string $button_url;

    public function mount()
    {
        $this->alert = FlashAlert::first();
        $this->is_active = $this->alert->is_active;
        $this->type = $this->alert->type;
        $this->title = $this->alert->title;
        $this->message = $this->alert->message ?? '';
        $this->button_text = $this->alert->button_text ?? '';
        $this->button_url = $this->alert->button_url ?? '';
    }

    public function save()
    {
        $this->alert->update([
            'is_active' => $this->is_active,
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'button_text' => $this->button_text,
            'button_url' => $this->button_url,
        ]);

        $this->dispatch('alert-saved');
    }

    public function toggle()
    {
        $this->is_active = !$this->is_active;
        $this->save();
    }
}; ?>

<div class="grid lg:grid-cols-2 gap-8">
    <!-- 1. Controls Section -->
    <div class="space-y-6">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-3xl p-8 shadow-xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-festa-gold/10 rounded-full blur-2xl group-hover:bg-festa-gold/20 transition duration-700"></div>
            
            <div class="flex items-center justify-between mb-8 relative z-10">
                <div>
                    <h2 class="text-2xl font-black font-heading text-zinc-900 dark:text-white">{{ __('Flash Info Direct') }}</h2>
                    <p class="text-sm text-zinc-500">{{ __('Gérez les alertes en temps réel sur le site.') }}</p>
                </div>
                
                <!-- Big Switch -->
                <button wire:click="toggle" 
                    class="relative inline-flex h-10 w-20 items-center rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-festa-gold focus:ring-offset-2 {{ $is_active ? 'bg-green-500' : 'bg-zinc-200 dark:bg-zinc-700' }}">
                    <span class="sr-only">{{ __('Activer l\'alerte') }}</span>
                    <span class="{{ $is_active ? 'translate-x-11' : 'translate-x-1' }} inline-block h-8 w-8 transform rounded-full bg-white shadow transition-transform duration-300 flex items-center justify-center">
                        <flux:icon.bolt class="size-4 {{ $is_active ? 'text-green-500' : 'text-zinc-400' }}" />
                    </span>
                </button>
            </div>

            <form wire:submit="save" class="space-y-6 relative z-10">
                
                <!-- Type Selection -->
                <div>
                    <label class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2 block">{{ __('Niveau d\'urgence') }}</label>
                    <div class="grid grid-cols-3 gap-3">
                        <button type="button" wire:click="$set('type', 'info'); save()" class="p-3 rounded-xl border-2 flex flex-col items-center gap-2 transition {{ $type === 'info' ? 'border-blue-500 bg-blue-500/10 text-blue-600' : 'border-zinc-200 dark:border-zinc-700 opacity-50 hover:opacity-100' }}">
                            <div class="size-3 rounded-full bg-blue-500"></div>
                            <span class="text-xs font-bold">{{ __('Info') }}</span>
                        </button>
                        <button type="button" wire:click="$set('type', 'warning'); save()" class="p-3 rounded-xl border-2 flex flex-col items-center gap-2 transition {{ $type === 'warning' ? 'border-orange-500 bg-orange-500/10 text-orange-600' : 'border-zinc-200 dark:border-zinc-700 opacity-50 hover:opacity-100' }}">
                            <div class="size-3 rounded-full bg-orange-500 animate-pulse"></div>
                            <span class="text-xs font-bold">{{ __('Attention') }}</span>
                        </button>
                        <button type="button" wire:click="$set('type', 'danger'); save()" class="p-3 rounded-xl border-2 flex flex-col items-center gap-2 transition {{ $type === 'danger' ? 'border-red-500 bg-red-500/10 text-red-600' : 'border-zinc-200 dark:border-zinc-700 opacity-50 hover:opacity-100' }}">
                            <div class="size-3 rounded-full bg-red-500 animate-ping"></div>
                            <span class="text-xs font-bold">{{ __('Urgence') }}</span>
                        </button>
                    </div>
                </div>

                <!-- Content Fields -->
                <flux:input wire:model="title" wire:blur="save" :label="__('Titre de l\'alerte')" placeholder="Ex: Changement de lieu..." />
                
                <flux:textarea wire:model="message" wire:blur="save" :label="__('Message détaillé')" placeholder="Ex: Le concert de ce soir est déplacé..." rows="3" />

                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="button_text" wire:blur="save" :label="__('Texte du bouton (Optionnel)')" placeholder="Ex: Voir le plan" />
                    <flux:input wire:model="button_url" wire:blur="save" :label="__('Lien du bouton (Optionnel)')" placeholder="https://..." />
                </div>

                <!-- Feedback Message -->
                <div x-data="{ show: false }" x-on:alert-saved.window="show = true; setTimeout(() => show = false, 2000)" class="h-6 flex items-center justify-end">
                    <span x-show="show" x-transition class="text-xs font-bold text-green-500 flex items-center gap-1">
                        <flux:icon.check-circle class="size-4" /> {{ __('Modifications enregistrées !') }}
                    </span>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Live Preview Section -->
    <div class="relative">
        <div class="sticky top-6">
            <h3 class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-4">{{ __('Aperçu visiteur (Temps Réel)') }}</h3>
            
            <!-- Phone Mockup -->
            <div class="relative mx-auto border-zinc-800 bg-zinc-800 border-[14px] rounded-[2.5rem] h-[600px] w-[300px] shadow-xl overflow-hidden">
                <div class="h-[32px] w-[3px] bg-zinc-800 absolute -start-[17px] top-[72px] rounded-s-lg"></div>
                <div class="h-[46px] w-[3px] bg-zinc-800 absolute -start-[17px] top-[124px] rounded-s-lg"></div>
                <div class="h-[46px] w-[3px] bg-zinc-800 absolute -start-[17px] top-[178px] rounded-s-lg"></div>
                <div class="h-[64px] w-[3px] bg-zinc-800 absolute -end-[17px] top-[142px] rounded-e-lg"></div>
                
                <div class="rounded-[2rem] overflow-hidden w-full h-full bg-zinc-950 relative">
                    <!-- Website Background (Blurry) -->
                    <div class="absolute inset-0 bg-cover bg-center opacity-50 blur-sm scale-110" style="background-image: url('https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=2070');"></div>
                    
                    <!-- The Popup Preview -->
                    <div class="absolute inset-0 flex items-center justify-center p-4 {{ $is_active ? 'opacity-100' : 'opacity-0 pointer-events-none' }} transition duration-500">
                        <!-- Popup Body -->
                        <div class="w-full bg-zinc-900 border border-white/10 rounded-2xl shadow-2xl p-6 relative overflow-hidden text-center transform scale-100 transition duration-300">
                            
                            <!-- Border Glow based on Type -->
                            <div class="absolute top-0 left-0 w-full h-1 
                                {{ $type === 'danger' ? 'bg-red-500' : ($type === 'warning' ? 'bg-orange-500' : 'bg-blue-500') }}">
                            </div>

                            <!-- Icon -->
                            <div class="mb-4 inline-flex p-3 rounded-full 
                                {{ $type === 'danger' ? 'bg-red-500/10 text-red-500' : ($type === 'warning' ? 'bg-orange-500/10 text-orange-500' : 'bg-blue-500/10 text-blue-500') }}">
                                <flux:icon.megaphone class="size-6 animate-pulse" />
                            </div>

                            <h3 class="text-white font-bold text-lg mb-2 leading-tight">{{ $title ?: 'Titre...' }}</h3>
                            
                            <p class="text-zinc-400 text-sm mb-6 leading-relaxed">{{ $message ?: 'Message...' }}</p>

                            @if($button_text)
                                <a href="#" class="block w-full py-3 rounded-xl font-bold text-sm uppercase tracking-wider mb-3 transition
                                    {{ $type === 'danger' ? 'bg-red-600 text-white hover:bg-red-700' : ($type === 'warning' ? 'bg-orange-500 text-white hover:bg-orange-600' : 'bg-blue-600 text-white hover:bg-blue-700') }}">
                                    {{ $button_text }}
                                </a>
                            @endif

                            <button class="text-zinc-500 text-xs uppercase tracking-widest font-bold hover:text-white transition">{{ __('Fermer') }}</button>
                        </div>
                    </div>

                    <!-- Off State Overlay -->
                    @if(!$is_active)
                        <div class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center backdrop-blur-sm">
                            <flux:icon.eye-slash class="size-12 text-white/20 mb-2" />
                            <p class="text-white/50 text-xs font-bold uppercase tracking-widest">{{ __('Alerte Désactivée') }}</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

