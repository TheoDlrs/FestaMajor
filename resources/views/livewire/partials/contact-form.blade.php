<?php

use Livewire\Volt\Component;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use App\Mail\ContactFormMail;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $category = '';
    public string $message = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'category' => 'required|string',
        'message' => 'required|string|min:10',
    ];

    public function sendMessage()
    {
        $this->validate();

        // Anti-Spam Logic (Laravel Native)
        $key = 'contact-form:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            $this->dispatch('message-sent', 
                message: "Trop de tentatives. Veuillez patienter $seconds secondes.",
                isError: true
            );
            return;
        }

        RateLimiter::hit($key, 60); // Bloque pour 60 secondes

        // Email Routing Logic
        $recipient = match ($this->category) {
            'press' => 'wassila.pla-amour@otstcyp.com',
            'part' => 'culture-evenements@otstcyp.com',
            default => 'contact@otstcyp.com',
        };

        $subjectMap = [
            'info' => 'Informations Générales',
            'press' => 'Presse & Média',
            'part' => 'Partenariat',
            'other' => 'Autre demande',
        ];

        // Save to Database
        ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'category' => $this->category,
            'message' => $this->message,
        ]);

        // Send Email
        try {
            Mail::to($recipient)->send(new ContactFormMail([
                'name' => $this->name,
                'email' => $this->email,
                'subject' => $subjectMap[$this->category] ?? 'Contact Site Web',
                'message' => $this->message,
            ]));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail sending failed: ' . $e->getMessage());
        }

        $this->reset(['name', 'email', 'category', 'message']);
        
        $this->dispatch('message-sent', message: 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.');
    }
}; ?>

<div class="relative rounded-[2rem] overflow-hidden bg-white/[0.03] border border-white/10 p-8 md:p-12 backdrop-blur-2xl shadow-2xl">
    <h3 class="font-heading text-3xl font-bold text-white mb-8">{{ __('Contactez-nous') }}</h3>
    
    <!-- Success/Error Message -->
    <div 
        x-data="{ show: false, text: '', isError: false }"
        x-on:message-sent.window="show = true; text = $event.detail.message; isError = $event.detail.isError || false; setTimeout(() => show = false, 5000)"
        x-show="show"
        x-transition
        :class="isError ? 'bg-red-500/20 border-red-500/50 text-red-400' : 'bg-green-500/20 border-green-500/50 text-green-400'"
        class="mb-6 p-4 border rounded-xl text-sm font-bold"
        style="display: none;"
    >
        <span x-text="text"></span>
    </div>

    <form wire:submit="sendMessage" class="space-y-6">
        <div class="grid md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider ml-1">{{ __('Nom Complet') }}</label>
                <input type="text" wire:model="name" placeholder="Jean Dupont" class="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-4 text-white focus:border-festa-gold outline-none transition">
                @error('name') <span class="text-red-500 text-[10px] uppercase font-bold">{{ $message }}</span> @enderror
            </div>
            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider ml-1">{{ __('Email') }}</label>
                <input type="email" wire:model="email" placeholder="jean@exemple.com" class="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-4 text-white focus:border-festa-gold outline-none transition">
                @error('email') <span class="text-red-500 text-[10px] uppercase font-bold">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <div class="space-y-2">
            <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider ml-1">{{ __('Catégorie') }}</label>
            <div class="relative">
                <select wire:model="category" class="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-4 text-white focus:border-festa-gold outline-none appearance-none transition">
                    <option value="" selected>{{ __('Sélectionnez un sujet') }}</option>
                    <option value="info">{{ __('Informations Générales') }}</option>
                    <option value="press">{{ __('Presse & Média') }}</option>
                    <option value="part">{{ __('Partenariat') }}</option>
                    <option value="other">{{ __('Autre') }}</option>
                </select>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-400">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
            @error('category') <span class="text-red-500 text-[10px] uppercase font-bold">{{ $message }}</span> @enderror
        </div>

        <div class="space-y-2">
            <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider ml-1">{{ __('Message') }}</label>
            <textarea rows="4" wire:model="message" placeholder="{{ __('Votre message...') }}" class="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-4 text-white focus:border-festa-gold outline-none resize-none transition"></textarea>
            @error('message') <span class="text-red-500 text-[10px] uppercase font-bold">{{ $message }}</span> @enderror
        </div>
        
        <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-10 py-4 bg-festa-gold text-festa-red-dark font-black uppercase tracking-widest text-sm rounded-xl shadow-lg hover:scale-105 transition duration-300 flex items-center justify-center gap-2">
            <span wire:loading.remove>{{ __('Envoyer le message') }}</span>
            <span wire:loading>{{ __('Envoi en cours...') }}</span>
        </button>
    </form>
</div>
