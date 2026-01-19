<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');
        $this->dispatch('password-updated');
    }
}; ?>

<div class="bg-zinc-900/50 border border-white/10 rounded-3xl p-8 relative overflow-hidden backdrop-blur-xl">
    <div class="absolute top-0 right-0 w-32 h-32 bg-festa-gold/5 rounded-full blur-[40px] pointer-events-none"></div>

    <div class="relative z-10">
        <h3 class="font-heading text-2xl text-white font-bold mb-6 flex items-center gap-3">
            <flux:icon.lock-closed class="size-6 text-festa-gold" />
            Mot de passe
        </h3>

        <form wire:submit="updatePassword" class="space-y-6">
            <div class="space-y-2">
                <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider ml-1">Mot de passe actuel</label>
                <input wire:model="current_password" type="password" required autocomplete="current-password" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-festa-gold focus:ring-1 focus:ring-festa-gold outline-none transition duration-300">
                @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider ml-1">Nouveau mot de passe</label>
                    <input wire:model="password" type="password" required autocomplete="new-password" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-festa-gold focus:ring-1 focus:ring-festa-gold outline-none transition duration-300">
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider ml-1">Confirmer le mot de passe</label>
                    <input wire:model="password_confirmation" type="password" required autocomplete="new-password" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-festa-gold focus:ring-1 focus:ring-festa-gold outline-none transition duration-300">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <x-action-message class="text-green-400 font-bold" on="password-updated">
                    {{ __('Mot de passe mis à jour.') }}
                </x-action-message>

                <button type="submit" class="px-6 py-2 bg-zinc-800 text-white font-bold uppercase tracking-widest text-xs rounded-lg border border-white/10 hover:bg-zinc-700 transition duration-300">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>
